<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Model\ServiceModel;
use K2D\News\Models\NewModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Nette\Neon\Neon;

class HomepagePresenter extends BasePresenter
{

	/* @inject */
	public ServiceModel $serviceModel;

	/* @inject */
	public NewModel $newModel;

	public function __construct(ServiceModel $serviceModel, NewModel $newModel)
	{
		parent::__construct();
		$this->serviceModel = $serviceModel;
		$this->newModel = $newModel;
	}

	public function renderDefault(): void
	{
		$this->template->services = $this->serviceModel->getPublicServices();
		$this->template->news = $this->newModel->getPublicNews('cs')->limit(3);
	}

	protected function createComponentContactForm(): Form
	{
		$form = new Form();

		$form->addText('name', 'Jméno a příjmení')
			->addRule(Form::MAX_LENGTH, 'Maximálné délka je %s znaků', 120)
			->setRequired('Musíte uvést Vaše jméno a příjmení');

		$form->addText('phone', 'Telefonní číslo')
			->setRequired('Musíte uvést telefonní číslo');

		$form->addEmail('email', 'Emailová adresa')
			->addRule(Form::MAX_LENGTH, 'Maximálné délka je %s znaků', 120)
			->setRequired('Musíte uvést Vaši emailovou adresu');

		$form->addTextArea('message', 'Zpráva')
			->addRule($form::MAX_LENGTH, 'Zpráva je příliš dlouhá', 10000);

		$form->addInvisibleReCaptcha('recaptcha')
			->setMessage('Jste opravdu člověk?');

		$form->addSubmit('submit', 'Odeslat');

		$form->onSubmit[] = function (Form $form) {
			try {
				$values = $form->getValues(true);

				if (!empty($values)) {
					$mail = new Message();

					$vars = $this->configuration->getAllVars();
					if (isset($vars['email']))
						$ownersEmail = $vars['email'];
					else
						$ownersEmail = 'fifa.urban@gmail.com';

					$mail->setFrom($values['email'], $values['name'])
						->addTo($ownersEmail)
						->setSubject('Podologie Žatec - zpráva z formuláře na webu')
						->setBody($values['message']);

					$parameters = Neon::decode(file_get_contents(__DIR__ . "/../../config/server/local.neon"));

					$mailer = new SmtpMailer([
						'host' => $parameters['mail']['host'],
						'username' => $parameters['mail']['username'],
						'password' => $parameters['mail']['password'],
						'secure' => $parameters['mail']['secure'],
					]);

					$mailer->send($mail);
				}

				$this->flashMessage('Email byl úspěšně odeslán!');
				$this->redirect('this#frm-contactForm');

			} catch (DriverException $e) {
				$this->flashMessage('Vaši zprávu se nepodařilo odeslat. Kontaktujte prosím správce webu na info@filipurban.cz', 'danger');
			}
		};

		return $form;
	}

}
