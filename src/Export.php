<?php

namespace Pneves001\Apidocs;

interface Export
{
    /**
     * Export Apidocs data into an array
     *
     * @param     Pneves001\Apidocs\Apidocs    $apidocs    apidocs
     * @return    array                                     apidocs data
     */
    public function export(Apidocs $apidocs): array;
}
