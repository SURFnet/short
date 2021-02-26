<?php

namespace App\Controller\Admin;

use App\Entity\Institution;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InstitutionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Institution::class;
    }
}
