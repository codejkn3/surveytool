<?php

/**
 * The SurveysController class is a Controller that shows a user a list of surveys
 * in the database.
 *
 * @author David Barnes
 * @copyright Copyright (c) 2013, David Barnes
 */
class SurveysController extends Controller
{
    /**
     * Handle the page request.
     *
     * @param array $request the page parameters from a form post or query string
     * 
     * JAN-28-2022 - updated parameters to queryRecordsWithWhereClause so that only
     * surveys owned by the current user will be returned. Corresponding changes
     * made to database structure as well.
     * J. Nealy
     */
    protected function handleRequest(&$request)
    {
        $user = $this->getUserSession();
        $userwhere = "owner_id = " . $user->login_id;
        $this->assign('user', $user); 

        // $surveys = Survey::queryRecords($this->pdo, ['sort' => 'survey_name']);
        $surveys = Survey::queryRecordsWithWhereClause($this->pdo, $userwhere);
        $this->assign('surveys', $surveys);

        if (isset($request['status']) && $request['status'] == 'deleted') {
            $this->assign('statusMessage', 'Survey deleted successfully');
        }
    }
}
