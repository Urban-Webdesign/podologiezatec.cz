<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Grid\ServiceGridFactory;
use App\AdminModule\Grid\ServiceGrid;
use App\Model\ServiceModel;
use K2D\Core\AdminModule\Component\CropperComponent\CropperComponent;
use K2D\Core\AdminModule\Component\CropperComponent\CropperComponentFactory;
use K2D\Core\AdminModule\Presenter\BasePresenter;
use K2D\Core\Helper\Helper;
use K2D\File\AdminModule\Component\DropzoneComponent\DropzoneComponent;
use K2D\File\AdminModule\Component\DropzoneComponent\DropzoneComponentFactory;
use K2D\Gallery\Models\GalleryModel;
use K2D\Gallery\Models\ImageModel;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Http\FileUpload;
use Nette\Utils\DateTime;
use Nette\Utils\Image;
use Nette\Utils\Strings;


/**
 * @property-read ActiveRow|null $service
 */
class ServicePresenter extends BasePresenter
{
	/** @inject */
	public ServiceModel $serviceModel;

	/** @inject */
	public GalleryModel $galleries;

	/** @inject */
	public ImageModel $images;

	/** @var ServiceGridFactory @inject */
	public $serviceGridFactory;

	/** @inject */
	public DropzoneComponentFactory $dropzoneComponentFactory;

	/** @inject */
	public CropperComponentFactory $cropperComponentFactory;

	public function renderEdit(?int $id = null): void
	{
		$this->template->service = null;

		if ($id !== null && $this->service !== null) {
			$service = $this->service->toArray();

			$form = $this['editForm'];
			$form->setDefaults($service);

			$this->template->service = $this->service;
		}
	}

	public function createComponentEditForm(): Form
	{
		$form = new Form();

		$form->addText('name', 'Název služby:')
			->addRule(Form::MAX_LENGTH, 'Maximální délka je %s znaků', 50)
			->setRequired('Název služby je povinné pole');

		$form->addInteger('price', 'Cena služby')
			->addRule(Form::MAX_LENGTH, 'Maximální délka je %s znaků', 7);

		$form->addInteger('duration', 'Délka služby (v minutách)')
			->addRule(Form::MAX_LENGTH, 'Maximální délka je %s znaků', 3);

		$form->addCheckbox('public', 'Zveřejnit službu')
			->setDefaultValue(true);

		$form->addSelect('gallery_id', 'Připojit galerii:')
			->setPrompt('None')
			->setItems($this->galleries->getForSelect());

		$form->addTextArea('description', 'Popis služby', 100, 20)
			->setHtmlAttribute('class', 'form-wysiwyg');

		$form->addSubmit('save', 'Uložit');

		$form->onSubmit[] = function (Form $form) {
			$values = $form->getValues(true);
			$values['slug'] = Strings::webalize($values['name']);
			$service = $this->service;

			if ($service === null) {
				$service = $this->serviceModel->insert($values);
				$this->flashMessage('Služba vytvořena');
			} else {
				$service->update($values);
				$this->flashMessage('Služba upravena');
			}

			$this->redirect('this', ['id' => $service->id]);
		};

		return $form;
	}

	public function handleUploadFiles(): void
	{
		$fileUploads = $this->getHttpRequest()->getFiles();
		$fileUpload = reset($fileUploads);

		if (!($fileUpload instanceof FileUpload)) {
			return;
		}

		if ($fileUpload->isOk() && $fileUpload->isImage()) {
			$image = $fileUpload->toImage();
			$link = WWW . '/upload/services/' . $this->service->id . '/';
			$fileName = Helper::generateFileName($fileUpload);

			if (!file_exists($link)) {
				Helper::mkdir($link);
			}

			if ($image->getHeight() > 600 || $image->getWidth() > 800) {
				$image->resize(800, 600);
			}

			$image->save($link . $fileName);
			$this->service->update(['cover' => $fileName]);
		}
	}

	public function handleRedrawFiles(): void
	{
		$this->redirect('this');
	}

	public function handleCropImage(): void
	{
		$this->showModal('cropper');
	}

	public function handleDeleteImage(): void
	{
		unlink(WWW . '/upload/services/' . $this->service->id . '/' . $this->service->cover);
		$this->service->update(['cover' => null]);
		$this->flashMessage('Náhledový obrázek byl smazán');
		$this->redirect('this');
	}

	public function handleRotateLeft(string $slug): void
	{
		$this->rotateImage($slug, 90);
		$this->redrawControl('image');
	}

	public function handleRotateRight(string $slug): void
	{
		$this->rotateImage($slug, -90);
		$this->redrawControl('image');
	}

	protected function createComponentServiceGrid(): ServiceGrid
	{
		return $this->serviceGridFactory->create();
	}

	protected function createComponentDropzone(): DropzoneComponent
	{
		$control = $this->dropzoneComponentFactory->create();
		$control->setPrompt('Nahrajde obrázek přetažením nebo klikněte zde.');
		$control->setUploadLink($this->link('uploadFiles!'));
		$control->setRedrawLink($this->link('redrawFiles!'));

		return $control;
	}

	protected function createComponentCropper(): CropperComponent
	{
		$cropper = $this->cropperComponentFactory->create();

		if ($this->service->cover !== null) {
			$cropper->setImagePath('upload/services/' . $this->service->id . '/' . $this->service->cover)
				->setAspectRatio((float) $this->configuration->img_resize);
		}

		$cropper->onCrop[] = function (): void {
			$this->redirect('this');
		};

		return $cropper;
	}

	protected function getService(): ?ActiveRow
	{
		return $this->serviceModel->get($this->getParameter('id'));
	}

	private function rotateImage(string $slug, int $angle): void
	{
		$service = $this->serviceModel->getService($slug);
		$imageOriginalPath = WWW . '/upload/services/' . $service->id . '/' . $service->cover;
		$imageOriginal = Image::fromFile($imageOriginalPath);
		$imageOriginal->rotate($angle, 0)->save($imageOriginalPath);
	}
}
