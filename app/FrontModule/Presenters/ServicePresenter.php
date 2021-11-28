<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Model\ServiceModel;

class ServicePresenter extends BasePresenter
{

	/* @inject */
	public ServiceModel $serviceModel;


	public function __construct(ServiceModel $serviceModel)
	{
		parent::__construct();
		$this->serviceModel = $serviceModel;
	}

	public function renderDefault(): void
	{
		$this->template->services = $this->serviceModel->getPublicServices();
	}

	public function renderShow($slug): void
	{
		$this->template->service = $this->serviceModel->getService($slug);
	}
}
