<?php

namespace App\Controllers;

use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
// use App\Model\Group;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use Phalcon\Http\Request;

class SurveyController extends CrudResourceController
{
    public function createSurveyDefinition()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);

        $organization = $creator['organization']->organization_id;
        $request = new Request();
        $data = $request->getJsonRawBody();
        $survey = new \App\Model\Survey();
        $survey->title = $data->title;
        $survey->description = $data->description;
        $survey->isEditable = $data->isEditable;
        $survey->isOlset = $data->isOlset;
        $survey->creator = $creator['account']->id;
        $survey->organization_id = $organization;
        if ($survey->save() == false) {
            $messagesErrors = array();
            foreach ($survey->getMessages() as $message) {
                // print_r($message);
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $surveyId = $survey->getWriteConnection()->lastInsertId();
            $response = [
              'code' => 1,
              'status' => 'Success',
              'data' => array('surveyId' => $surveyId),
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function getSurveyDefinition()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);
        $organization = $creator['organization']->organization_id;
        $surveys = Survey::find(
          [
              'conditions' => '	organization_id = ?1',
              'bind' => [
                  1 => $organization,
              ],
          ]
        );

        $response = [
          'code' => 1,
          'status' => 'Success',
          'data' => $surveys,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function updateSurveyDefinition($id){
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $creatorId = $session->getIdentity();
      }
      $request = new Request();
      $data = $request->getJsonRawBody();
      $creator = $this->getUserDetails($creatorId);
      $organization = $creator['organization']->organization_id;
      if ($creator['organization'] == null) {
          $response = [
        'code' => 0,
        'status' => 'Error',
        'data' => "Manager's organization not found!",
      ];

          return $this->createArrayResponse($response, 'data');
      }


      $survey = Survey::findFirst(
          [
          'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
          'bind' => [
              1 => $id,
              2 => $organization,
              3 => $creator['account']->id
          ],
      ]);

      if ($survey->id) {
      //  echo $department->id;die();
          if (isset($data->title)) {
              $survey->title = $data->title;
          }
          if (isset($data->description)) {
              $survey->description = $data->description;
          }
          if (isset($data->isEditable)) {
              $survey->isEditable = $data->isEditable;
          }
          if (isset($data->isOlset)) {
              $survey->isOlset = $data->isOlset;
          }
          if ($survey->save() == false) {
              $messagesErrors = array();
              foreach ($survey->getMessages() as $message) {
                  // print_r($message);
                  $messagesErrors[] = $message;
              }
              $response = [
                  'code' => 0,
                  'status' => 'Error',
                  'data' => $messagesErrors,
              ];
          } else {
            $response = [
                'code' => 1,
                'status' => 'Success'
            ];
          }
      } else {
          $response = [
            'code' => 0,
            'status' => 'You cannot edit this survey!',
          ];
      }

        return $this->createArrayResponse($response, 'data');
    }

    public function createQuestion($id){

          if ($this->authManager->loggedIn()) {
              $session = $this->authManager->getSession();
              $creatorId = $session->getIdentity();
          }
          $creator = $this->getUserDetails($creatorId);

          $organization = $creator['organization']->organization_id;
          $request = new Request();
          $data = $request->getJsonRawBody();

          $survey = Survey::findFirst(
              [
              'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
              'bind' => [
                  1 => $id,
                  2 => $organization,
                  3 => $creator['account']->id
              ],
          ]);

          if ($survey->id) {
            $surveyQuestion = new \App\Model\SurveyQuestion();
            $surveyQuestion->question = $data->question;
            $surveyQuestion->description = $data->description;
            $surveyQuestion->answered_type = $data->answered_type;
            $surveyQuestion->question_order = $data->question_order;
            $surveyQuestion->survey_id = $id;
            if ($surveyQuestion->save() == false) {
                $messagesErrors = array();
                foreach ($surveyQuestion->getMessages() as $message) {
                    // print_r($message);
                    $messagesErrors[] = $message;
                }
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $messagesErrors,
                ];
            } else {
                $surveyId = $surveyQuestion->getWriteConnection()->lastInsertId();
                $response = [
                  'code' => 1,
                  'status' => 'Success',
                  'data' => array('surveyQuestion' => $surveyId),
                ];
            }
          }else{
            $response = [
                'code' => 0,
                'status' => 'Unauthorized user!',
            ];
          }





          return $this->createArrayResponse($response, 'data');
    }

    public function getQuestion($id){
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $creatorId = $session->getIdentity();
      }

      $creator = $this->getUserDetails($creatorId);
      $organization = $creator['organization']->organization_id;
      $survey = Survey::findFirst(
          [
          'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
          'bind' => [
              1 => $id,
              2 => $organization,
              3 => $creator['account']->id
          ],
      ]);

      if ($survey->id) {
        
      $surveyQuestion = SurveyQuestion::find(
        [
          'conditions' => 'survey_id = ?1',
          'bind' => [
              1 => $id
       ]]
      );

      $response = [
        'code' => 1,
        'status' => 'Success',
        'data' => $surveyQuestion,
      ];
    }else{
      $response = [
          'code' => 0,
          'status' => 'Unauthorized user!',
      ];
    }
      return $this->createArrayResponse($response, 'data');
    }

    public static function getUserDetails($userId)
    {
        $user = User::findFirst(
        [
            'conditions' => 'id = ?1',
            'bind' => [
                1 => $userId,
            ],
        ]
    );
        if ($user) {
            $organization = UserOrganization::findFirst(
            [
                'conditions' => 'user_id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );

            if ($organization) {
                return array('account' => $user, 'organization' => $organization);
            } else {
                return array('account' => $user, 'organization' => null);
            }
        } else {
            return null;
        }
    }
}
