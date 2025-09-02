<?php
interface SignupInterface
{

    /**
     * findByDocumentTemp function
     * @param int $documento
     * @param int $coddoc
     * @param string $calemp
     * @return object
     */
    public function findByDocumentTemp($documento, $coddoc, $calemp = '');

    /**
     * createSignupService function
     * @param array $data
     * @return void
     */
    public function createSignupService($data);

    /**
     * getSolicitud function
     * @return object
     */
    public function getSolicitud();
}
