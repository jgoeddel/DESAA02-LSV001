<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Cron\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Cron\CronDatabase;
use App\Pages\Email\MVC\EmailController;
use App\Pages\Home\IndexDatabase;
use App\PHPMailer\Exception;

class CronController extends AbstractController
{
    # Construct
    private CronDatabase $cronDatabase;

    public function __construct(
        CronDatabase $cronDatabase
    ){
        $this->cronDatabase = $cronDatabase;
    }

}
