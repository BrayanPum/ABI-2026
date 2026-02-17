<?php

namespace App\Models\Student;

use App\Models\ContentVersion;

/**
 * Extended model that keeps content-version access on the student connection
 * so permissions remain aligned with the student role.
 */
class StudentContentVersion extends ContentVersion
{
    protected $table = 'content_version';

    protected $connection = 'mysql_student';
}
