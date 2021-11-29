<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use K2D\Gallery\Models\ImageModel;
use K2D\News\Models\NewModel;
use Nette\Utils\Image;

class NewPresenter extends BasePresenter
{

	/* @inject */
	public NewModel $newModel;

	/* @inject */
	public ImageModel $imageModel;


	public function __construct(NewModel $newModel, ImageModel $imageModel)
	{
		parent::__construct();
		$this->newModel = $newModel;
		$this->imageModel = $imageModel;
	}

	public function renderDefault(): void
	{
		$this->template->news = $this->newModel->getPublicNews('cs');
	}

	public function renderShow($slug): void
	{
		$new = $this->newModel->getNew($slug, 'cs');
		$this->template->new = $new;

		// get images
		if ($new->gallery_id != NULL)
			$this->template->images = $this->imageModel->getImagesByGallery($new->gallery_id);
	}
}
