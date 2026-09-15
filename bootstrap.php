<?php
declare(strict_types=1);
if (ob_get_level() === 0) { ob_start(); }
require_once __DIR__.'/app/Core/Database.php';
require_once __DIR__.'/app/Core/Security.php';
require_once __DIR__.'/app/Core/Response.php';
require_once __DIR__.'/app/Services/AuditService.php';
require_once __DIR__.'/app/Services/AuthService.php';
require_once __DIR__.'/app/Services/RbacService.php';
require_once __DIR__.'/app/Services/DocumentService.php';
require_once __DIR__.'/app/Services/NumberingService.php';
require_once __DIR__.'/app/Services/NotificationService.php';
require_once __DIR__.'/app/Services/WorkflowService.php';
require_once __DIR__.'/app/Services/FileService.php';
require_once __DIR__.'/app/Services/PdfService.php';
require_once __DIR__.'/app/Services/OperationsService.php';
Security::startSession();
require_once __DIR__.'/app/Services/LetterService.php';
require_once __DIR__.'/app/Services/AgendaService.php';
