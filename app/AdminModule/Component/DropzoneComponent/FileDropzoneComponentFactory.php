<?php declare(strict_types = 1);

namespace App\AdminModule\Component\DropzoneComponent;

use App\AdminModule\Component\DropzoneComponent\FileDropzoneComponent;

interface FileDropzoneComponentFactory
{

	public function create(): FileDropzoneComponent;

}
