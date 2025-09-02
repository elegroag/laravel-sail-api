<?php

interface ReportGenerator
{
    public function generateReport($title, $file, $columns);
}
