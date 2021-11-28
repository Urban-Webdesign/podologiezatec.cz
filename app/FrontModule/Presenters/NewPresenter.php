<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use K2D\News\Models\NewModel;

class NewPresenter extends BasePresenter
{

	/* @inject */
	public NewModel $newModel;


	public function __construct(NewModel $newModel)
	{
		parent::__construct();
		$this->newModel = $newModel;
	}

	public function renderDefault(): void
	{
		$this->template->news = $this->newModel->getPublicNews('cs');
	}

	public function renderShow($slug): void
	{
		$this->template->new = $this->newModel->getNew($slug, 'cs');
	}
}
