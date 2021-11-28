<?php declare(strict_types=1);

namespace App\AdminModule\Grid;

use App\Model\ServiceModel;
use K2D\Core\AdminModule\Grid\BaseV2Grid;

use K2D\Core\Models\ConfigurationModel;
use K2D\Gallery\Models\GalleryModel;
use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Container;

class ServiceGrid extends BaseV2Grid
{


	/** @var ServiceModel */
	private ServiceModel $serviceModel;

	public ConfigurationModel $configuration;

	public function __construct(ServiceModel $serviceModel)
	{
		parent::__construct();
		$this->serviceModel = $serviceModel;
	}

	protected function build(): void
	{
		$this->model = $this->serviceModel;

		parent::build();

		$this->setDefaultOrderBy('created', true);
		$this->setFilterFactory([$this, 'gridFilterFactory']);

		$this->addColumn('name', 'Název');
		$this->addColumn('duration', 'Délka');
		$this->addColumn('price', 'Cena');
		$this->addColumn('gallery', 'Galerie');
		$this->addColumn('public', 'Veřejné');
		$this->addColumn('updated', 'Poslední úprava')->setSortable();
		$this->addColumn('created', 'Vytvořeno')->setSortable();

		$this->addRowAction('edit', 'Upravit', static function (): void {});
		$this->addRowAction('delete', 'Smazat', static function (ActiveRow $record): void {
			if ($record->cover) {
				unlink(WWW . '/upload/services/' . $record->id . '/' . $record->cover);
			}

			$record->delete();
		})
			->setProtected(false)
			->setConfirmation('Jste si jisti, že chcete tuto službu smazat?');
	}

	public function gridFilterFactory(Container $c): void
	{
		$c->addText('name', 'Název služby')->setHtmlAttribute('placeholder', 'Filtrování podle názvu');
	}
}
