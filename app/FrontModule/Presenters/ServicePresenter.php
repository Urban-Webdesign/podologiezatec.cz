<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Model\ServiceModel;
use K2D\Gallery\Models\ImageModel;

class ServicePresenter extends BasePresenter
{

	/* @inject */
	public ServiceModel $serviceModel;

	/* @inject */
	public ImageModel $imageModel;


	public function __construct(ServiceModel $serviceModel, ImageModel $imageModel)
	{
		parent::__construct();
		$this->serviceModel = $serviceModel;
		$this->imageModel = $imageModel;
	}

	public function renderDefault(): void
	{
		$this->template->services = $this->serviceModel->getPublicServices();
	}

	public function renderShow($slug): void
	{
		$service = $this->serviceModel->getService($slug);
		$this->template->service = $service;

		// get images
		if ($service->gallery_id != NULL)
			$this->template->images = $this->imageModel->getImagesByGallery($service->gallery_id);
	}
}
