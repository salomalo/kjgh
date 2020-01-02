<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.4.0
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class acymuserClass extends acymClass
{
    var $table = 'user';
    var $pkey = 'id';

    var $sendWelcome = true;
    var $sendUnsubscribe = true;

    var $checkVisitor = true;
    var $restrictedFields = ['id', 'key', 'confirmed', 'active', 'cms_id', 'creation_date'];
    var $allowModif = false;
    var $requireId = false;
    var $sendConf = true;
    var $forceConf = false;
    var $confirmationSentSuccess = false;

    public function __construct()
    {
        parent::__construct();

        $missingKey = acym_loadResultArray('SELECT `id` FROM #__acym_user WHERE `key` IS NULL LIMIT 5000');
        if (!empty($missingKey)) {
            $newValues = [];
            foreach ($missingKey as $oneUserId) {
                $newValues[] = intval($oneUserId).','.acym_escapeDB(acym_generateKey(14));
            }
            acym_query('INSERT INTO #__acym_user (`id`, `key`) VALUES ('.implode('),(', $newValues).') ON DUPLICATE KEY UPDATE `key` = VALUES(`key`)');
        }
    }

    public function getMatchingElements($settings = [])
    {
        $columns = 'user.*';
        if (!empty($settings['columns'])) {
            foreach ($settings['columns'] as $key => $value) {
                $settings['columns'][$key] = $key === 'join' ? $value : 'user.'.$value;
            }
            $columns = implode(', ', $settings['columns']);
        }

        $query = 'SELECT '.$columns.' FROM #__acym_user AS user';
        $queryCount = 'SELECT COUNT(user.id) FROM #__acym_user AS user';
        $queryStatus = 'SELECT COUNT(id) AS number, active FROM #__acym_user AS user';
        $filters = [];

        if (!empty($settings['join'])) $query .= $this->getJoinForQuery($settings['join']);


        if (!empty($settings['search'])) {
            $completeSearchField = $this->searchForMultliplaValueField($settings['search']);
            $completeSearch = '';
            if (!empty($completeSearchField)) {
                foreach ($completeSearchField as $oneField) {
                    $completeSearch .= ' OR userfield.value LIKE '.acym_escapeDB('%'.$oneField.'%');
                }
            }
            $searchValue = acym_escapeDB('%'.$settings['search'].'%');
            $searchFilter = 'user.email LIKE '.$searchValue.' OR user.name LIKE '.$searchValue;
            $searchFilter .= ' OR user.id IN (SELECT userfield.user_id FROM #__acym_user_has_field AS userfield INNER JOIN #__acym_field AS field ON userfield.field_id = field.id 
            WHERE field.backend_listing = 1 AND (userfield.value LIKE '.$searchValue.' '.$completeSearch.'))';

            $filters[] = $searchFilter;
        }

        if (!empty($filters)) {
            $queryStatus .= ' WHERE ('.implode(') AND (', $filters).')';
        }

        if (!empty($settings['status'])) {
            $allowedStatus = [
                'active' => 'active = 1',
                'inactive' => 'active = 0',
            ];
            if (empty($allowedStatus[$settings['status']])) {
                die('Injection denied');
            }
            $filters[] = 'user.'.$allowedStatus[$settings['status']];
        }

        if (isset($settings['hiddenElements'])) {
            if (empty($settings['hiddenElements'])) {
                $filters[] = 'user.id IS NOT NULL';
            } else {
                acym_arrayToInteger($settings['hiddenElements']);
                $filters[] = 'user.id NOT IN('.implode(',', $settings['hiddenElements']).')';
            }
        }

        if (isset($settings['onlyElements'])) {
            if (empty($settings['onlyElements'])) {
                $filters[] = 'user.id IS NULL';
            } else {
                acym_arrayToInteger($settings['onlyElements']);
                $filters[] = 'user.id IN('.implode(',', $settings['onlyElements']).')';
            }
        }

        if (!empty($settings['showOnlySelected'])) {
            if (!empty($settings['selectedUsers'])) {
                acym_arrayToInteger($settings['selectedUsers']);
                $filters[] = 'user.id IN('.implode(',', $settings['selectedUsers']).')';
            } else {
                return ['users' => [], 'total' => 0];
            }
        }

        if (!empty($settings['relation']) && !empty($settings['relation']['target']) && !empty($settings['relation']['target_id']) && isset($settings['relation']['is_related'])) {
            if ($settings['relation']['is_related']) {
                switch ($settings['relation']['target']) {
                    case 'list':
                        $join = ' JOIN #__acym_user_has_list AS userlist ON user.id = userlist.user_id';
                        $filters[] = '(userlist.list_id = '.acym_escapeDB($settings['relation']['target_id']).') AND (userlist.status = 1)';
                        break;
                    default:
                        $join = '';
                        break;
                }
            } else {
                switch ($settings['relation']['target']) {
                    case 'list':
                        $join = ' LEFT JOIN #__acym_user_has_list AS userlist ON user.id = userlist.user_id AND userlist.list_id = '.acym_escapeDB($settings['relation']['target_id']);
                        $filters[] = '(userlist.user_id IS NULL OR userlist.status = 0)';
                        break;
                    default:
                        $join = '';
                        break;
                }
            }

            $query .= $join;
            $queryCount .= $join;
        }

        if (!empty($filters)) {
            $query .= ' WHERE ('.implode(') AND (', $filters).')';
            $queryCount .= ' WHERE ('.implode(') AND (', $filters).')';
        }

        if (!empty($settings['ordering']) && !empty($settings['ordering_sort_order'])) {
            $query .= ' ORDER BY user.'.acym_secureDBColumn($settings['ordering']).' '.acym_secureDBColumn(strtoupper($settings['ordering_sort_order']));
        }

        if (empty($settings['offset']) || $settings['offset'] < 0) {
            $settings['offset'] = 0;
        }

        if (empty($settings['elementsPerPage']) || $settings['elementsPerPage'] < 1) {
            $settings['elementsPerPage'] = acym_getCMSConfig('list_limit', 20);
        }

        $results['elements'] = acym_loadObjectList($query, '', $settings['offset'], $settings['elementsPerPage']);
        $results['total'] = acym_loadResult($queryCount);

        $usersPerStatus = acym_loadObjectList($queryStatus.' GROUP BY active', 'active');

        for ($i = 0 ; $i < 2 ; $i++) {
            $usersPerStatus[$i] = empty($usersPerStatus[$i]) ? 0 : $usersPerStatus[$i]->number;
        }

        $results['status'] = [
            'all' => array_sum($usersPerStatus),
            'active' => $usersPerStatus[1],
            'inactive' => $usersPerStatus[0],
        ];

        return $results;
    }

    private function searchForMultliplaValueField($search)
    {
        $return = [];
        $fieldsWithMultipleValues = acym_loadObjectList('SELECT * FROM #__acym_field WHERE `value` LIKE '.acym_escapeDB('%"title":"'.$search.'"%'));
        if (!empty($fieldsWithMultipleValues)) {
            foreach ($fieldsWithMultipleValues as $oneField) {
                $oneField->value = json_decode($oneField->value, true);
                foreach ($oneField->value as $value) {
                    if (stripos($value['title'], $search) !== false) $return[] = $value['value'];
                }
            }
        }

        return $return;
    }

    public function getJoinForQuery($joinType)
    {
        if (strpos($joinType, 'join_list') !== false) {
            $listId = explode('-', $joinType);

            return ' LEFT JOIN #__acym_user_has_list as userlist ON user.id = userlist.user_id AND userlist.status = 1 AND userlist.list_id = '.intval($listId[1]);
        }

        return '';
    }

    public function getAll()
    {
        $query = 'SELECT * FROM #__acym_user';

        return acym_loadObjectList($query);
    }

    public function getOneById($id)
    {
        $query = 'SELECT * FROM #__acym_user WHERE id = '.intval($id).' LIMIT 1';

        return acym_loadObject($query);
    }

    public function getOneByCMSId($id)
    {
        $query = 'SELECT * FROM #__acym_user WHERE cms_id = '.intval($id).' LIMIT 1';

        return acym_loadObject($query);
    }


    public function getOneByEmail($email)
    {
        $query = "SELECT * FROM #__acym_user WHERE email = ".acym_escapeDB($email)." LIMIT 1";

        return acym_loadObject($query);
    }

    public function getUserSubscriptionById($userId, $key = 'id')
    {
        $query = 'SELECT list.id, list.name, list.color, list.active, list.visible, userlist.status, userlist.subscription_date, userlist.unsubscribe_date 
                FROM #__acym_list AS list 
                JOIN #__acym_user_has_list AS userlist 
                    ON list.id = userlist.list_id 
                WHERE userlist.user_id = '.intval($userId);

        return acym_loadObjectList($query, $key);
    }

    public function getAllListsUserSubscriptionById($userId, $key = 'id')
    {
        $query = 'SELECT list.id, list.name, list.color, list.active, list.visible, userlist.status, userlist.subscription_date, userlist.unsubscribe_date 
                FROM #__acym_list AS list 
                LEFT JOIN #__acym_user_has_list AS userlist 
                    ON list.id = userlist.list_id 
                    AND userlist.user_id = '.intval($userId);

        return acym_loadObjectList($query, $key);
    }

    public function getUsersSubscriptionsByIds($usersId)
    {
        $query = 'SELECT id, user_id, l.color, l.name
                FROM #__acym_list AS l
                JOIN #__acym_user_has_list AS userlist 
                    ON l.id = userlist.list_id
                WHERE user_id IN ('.implode(',', $usersId).')
                AND userlist.status = 1';

        return acym_loadObjectList($query);
    }

    public function getCountTotalUsers()
    {
        $query = 'SELECT COUNT(id) FROM #__acym_user';

        return acym_loadResult($query);
    }

    public function getSubscriptionStatus($userId, $listids = [])
    {
        $query = 'SELECT status, list_id FROM #__acym_user_has_list WHERE user_id = '.intval($userId);
        if (!empty($listids)) {
            acym_arrayToInteger($listids);
            $query .= ' AND list_id IN ('.implode(',', $listids).')';
        }

        return acym_loadObjectList($query, 'list_id');
    }

    public function identify($onlyValue = false)
    {
        $id = acym_getVar('int', "id", 0);
        $key = acym_getVar('string', "key", '');

        if (empty($id) || empty($key)) {
            $currentUserid = acym_currentUserId();
            if (!empty($currentUserid)) {
                return $this->getOneByCMSId($currentUserid);
            }
            if (!$onlyValue) {
                acym_enqueueMessage(acym_translation('ACYM_LOGIN'), 'error');
            }

            return false;
        }

        $userIdentified = acym_loadObject('SELECT * FROM #__acym_user WHERE `id` = '.intval($id).' AND `key` = '.acym_escapeDB($key));

        if (!empty($userIdentified)) {
            return $userIdentified;
        }

        if (!$onlyValue) {
            acym_enqueueMessage(acym_translation('INVALID_KEY'), 'error');
        }

        return false;
    }

    public function subscribe($userIds, $addLists)
    {
        if (empty($addLists)) return false;

        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        if (!is_array($addLists)) {
            $addLists = [$addLists];
        }

        $config = acym_config();
        $listClass = acym_get('class.list');
        $historyClass = acym_get('class.history');

        $confirmationRequired = $config->get('require_confirmation', 1);
        $subscribedToLists = false;
        $historyData = acym_translation_sprintf('ACYM_LISTS_NUMBERS', implode(', ', $addLists));

        foreach ($userIds as $userId) {
            $user = $this->getOneById($userId);
            if (empty($user)) continue;

            $currentSubscription = $this->getUserSubscriptionById($userId);

            $currentlySubscribed = [];
            $currentlyUnsubscribed = [];
            foreach ($currentSubscription as $oneList) {
                if ($oneList->status == 1) {
                    $currentlySubscribed[$oneList->id] = $oneList;
                }
                if ($oneList->status == 0) {
                    $currentlyUnsubscribed[$oneList->id] = $oneList;
                }
            }

            $subscribedLists = [];
            foreach ($addLists as $oneListId) {
                if (empty($oneListId) || !empty($currentlySubscribed[$oneListId])) continue;

                $subscription = new stdClass();
                $subscription->user_id = $userId;
                $subscription->list_id = $oneListId;
                $subscription->status = 1;
                $subscription->subscription_date = date('Y-m-d H:i:s', time());

                if (empty($currentSubscription[$oneListId])) {
                    acym_insertObject('#__acym_user_has_list', $subscription);
                } elseif (!empty($currentlyUnsubscribed[$oneListId])) {
                    acym_updateObject('#__acym_user_has_list', $subscription, ['user_id', 'list_id']);
                }

                $subscribedLists[] = $oneListId;
                $subscribedToLists = true;
            }

            $historyClass->insert($userId, 'subscribed', [$historyData]);

            acym_trigger('onAcymAfterUserSubscribe', [&$user, &$subscribedLists]);

            if ($confirmationRequired == 0 || $user->confirmed == 1) {
                $listClass->sendWelcome($userId, $subscribedLists);
            }
        }

        return $subscribedToLists;
    }

    public function unsubscribe($userIds, $lists)
    {
        if (empty($lists)) {
            return false;
        }

        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        if (!is_array($lists)) {
            $lists = [$lists];
        }

        $listClass = acym_get('class.list');
        $unsubscribedFromLists = false;
        foreach ($userIds as $userId) {
            $currentSubscription = $this->getUserSubscriptionById($userId);

            $currentlyUnsubscribed = [];
            foreach ($currentSubscription as $oneList) {
                if ($oneList->status == 0) {
                    $currentlyUnsubscribed[$oneList->id] = $oneList;
                }
            }

            $unsubscribedLists = [];
            foreach ($lists as $oneListId) {
                if (empty($oneListId) || !empty($currentlyUnsubscribed[$oneListId])) {
                    continue;
                }

                $subscription = new stdClass();
                $subscription->user_id = $userId;
                $subscription->list_id = $oneListId;
                $subscription->status = 0;
                $subscription->unsubscribe_date = date("Y-m-d H:i:s", time());

                if (empty($currentSubscription[$oneListId])) {
                    acym_insertObject('#__acym_user_has_list', $subscription);
                } else {
                    acym_updateObject('#__acym_user_has_list', $subscription, ['user_id', 'list_id']);
                }

                $unsubscribedLists[] = $oneListId;
                $unsubscribedFromLists = true;
            }

            $historyClass = acym_get('class.history');
            $historyData = acym_translation_sprintf('ACYM_LISTS_NUMBERS', implode(', ', $lists));
            $historyClass->insert($userId, 'unsubscribed', [$historyData]);

            acym_trigger('onAcymAfterUserUnsubscribe', [&$userId, &$unsubscribedLists]);

            $listClass->sendUnsubscribe($userId, $unsubscribedLists);
        }

        return $unsubscribedFromLists;
    }

    public function removeSubscription($userId, $listIds = null)
    {
        if (!is_array($userId)) $userId = [$userId];
        if (!is_array($listIds) || empty($listIds) || empty($userId)) return false;

        acym_arrayToInteger($listIds);
        $query = 'DELETE FROM #__acym_user_has_list WHERE user_id IN ('.implode(',', $userId).')';
        if (!empty($listIds)) $query .= ' AND list_id IN ('.implode(',', $listIds).')';

        return acym_query($query);
    }

    public function delete($elements)
    {
        if (!is_array($elements)) $elements = [$elements];
        acym_arrayToInteger($elements);

        if (empty($elements)) return 0;

        acym_trigger('onAcymBeforeUserDelete', [&$elements]);

        acym_query('DELETE FROM #__acym_user_has_list WHERE user_id IN ('.implode(',', $elements).')');
        acym_query('DELETE FROM #__acym_queue WHERE user_id IN ('.implode(',', $elements).')');
        acym_query('DELETE FROM #__acym_user_has_field WHERE user_id IN ('.implode(',', $elements).')');

        return parent::delete($elements);
    }

    public function save($user, $customFields = null, $ajax = false)
    {
        if (empty($user->email) && empty($user->id)) return false;

        if (empty($user->id) && !isset($user->active)) $user->active = 1;

        if (isset($user->email)) {
            $user->email = strtolower($user->email);
            if (!acym_isValidEmail($user->email)) {
                $this->errors[] = acym_translation('ACYM_VALID_EMAIL');

                return false;
            }
        }

        $config = acym_config();

        if (empty($user->id)) {
            $currentUserid = acym_currentUserId();
            $currentEmail = acym_currentUserEmail();
            if ($this->checkVisitor && !acym_isAdmin() && intval($config->get('allow_visitor', 1)) != 1 && (empty($currentUserid) || strtolower($currentEmail) != $user->email)) {
                $this->errors[] = acym_translation('ACYM_ONLY_LOGGED');

                return false;
            }
        }

        if (empty($user->id)) {
            if (empty($user->name) && $config->get('generate_name', 1)) {
                $user->name = ucwords(trim(str_replace(['.', '_', ')', ',', '(', '-', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0], ' ', substr($user->email, 0, strpos($user->email, '@')))));
            }

            if (empty($user->key)) $user->key = acym_generateKey(14);

            $user->creation_date = date('Y-m-d H:i:s', time());
        } elseif (!empty($user->confirmed)) {
            $oldUser = $this->getOneById($user->id);
            if (!empty($oldUser) && empty($oldUser->confirmed)) {
                $user->confirmation_date = date('Y-m-d H:i:s', time());
                $user->confirmation_ip = acym_getIP();
            }
        }

        foreach ($user as $oneAttribute => $value) {
            if (empty($value)) continue;

            $oneAttribute = trim(strtolower($oneAttribute));
            if (!in_array($oneAttribute, $this->restrictedFields)) {
                $user->$oneAttribute = strip_tags($value);
            }

            if (is_numeric($user->$oneAttribute)) continue;

            if (function_exists('mb_detect_encoding')) {
                if (mb_detect_encoding($user->$oneAttribute, 'UTF-8', true) != 'UTF-8') {
                    $user->$oneAttribute = utf8_encode($user->$oneAttribute);
                }
            } elseif (!preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$%xs', $user->$oneAttribute)) {
                $user->$oneAttribute = utf8_encode($user->$oneAttribute);
            }
        }

        if (empty($user->id)) {
            if (empty($user->cms_id) && !empty($user->email)) {
                $userCmsID = acym_loadResult('SELECT '.acym_secureDBColumn($this->cmsUserVars->id).' FROM '.$this->cmsUserVars->table.' WHERE '.acym_secureDBColumn($this->cmsUserVars->email).' = '.acym_escapeDB($user->email));
                if (!empty($userCmsID)) $user->cms_id = $userCmsID;
            }
            acym_trigger('onAcymBeforeUserCreate', [&$user]);
        } else {
            acym_trigger('onAcymBeforeUserModify', [&$user]);
        }

        $userID = parent::save($user);

        $fieldClass = acym_get('class.field');
        $fieldClass->store($userID, $customFields, $ajax);
        if (!empty($fieldClass->errors)) $this->errors = array_merge($this->errors, $fieldClass->errors);

        $historyClass = acym_get('class.history');
        if (empty($user->id)) {
            $user->id = $userID;
            $historyClass->insert($user->id, 'created');
            acym_trigger('onAcymAfterUserCreate', [&$user]);
        } else {
            $historyClass->insert($user->id, 'modified');
            acym_trigger('onAcymAfterUserModify', [&$user]);
        }

        $this->sendConfirmation($userID);

        return $userID;
    }

    public function saveForm()
    {
        $config = acym_config();
        $allowUserModifications = (bool)($config->get('allow_modif', 'data') == 'all') || $this->allowModif;
        $allowSubscriptionModifications = (bool)($config->get('allow_modif', 'data') != 'none') || $this->allowModif;

        $user = new stdClass();
        $user->id = acym_getCID('id');

        if (!$this->allowModif && !empty($user->id)) {
            $currentUser = $this->identify();
            if ($currentUser->id != $user->id) {
                $this->errors[] = acym_translation('ACYM_NOT_ALLOWED_MODIFY_USER');

                return false;
            }

            $allowUserModifications = true;
            $allowSubscriptionModifications = true;
        }

        $userData = acym_getVar('array', 'user', []);
        if (!empty($userData)) {
            foreach ($userData as $attribute => $value) {
                $user->$attribute = $value;
            }
        }

        if (empty($user->id) && empty($user->email)) {
            $this->errors[] = acym_translation('ACYM_VALID_EMAIL');

            return false;
        }

        if (!empty($user->email)) {
            if (empty($user->id)) {
                $user->id = 0;
            }
            $existUser = acym_loadObject('SELECT * FROM #__acym_user WHERE email = '.acym_escapeDB($user->email).' AND id != '.intval($user->id));
            if (!empty($existUser->id) && !$this->allowModif) {
                $this->errors[] = acym_translation('ACYM_ADDRESS_TAKEN');

                return false;
            }
        }

        if (!empty($user->id) && !empty($user->email)) {
            $existUser = $this->getOneById($user->id);
            if (trim(strtolower($user->email)) != strtolower($existUser->email)) {
                $user->confirmed = 0;
            }
        }

        $this->newUser = empty($user->id);
        if (empty($user->id) || $allowUserModifications) {
            if (isset($user->confirmed) && $user->confirmed != 1) {
                $user->confirmed = 0;
            }
            if (isset($user->active) && $user->active != 1) {
                $user->active = 0;
            }
            $customFieldData = acym_getVar('array', 'customField', []);
            $id = $this->save($user, $customFieldData);
            $allowSubscriptionModifications = true;
        } else {
            $id = $user->id;
            if (isset($user->confirmed) && empty($user->confirmed)) {
                $this->sendConfirmation($id);
            }
        }

        if (empty($id)) {
            return false;
        }
        $formData = acym_getVar('array', 'data', []);

        acym_setVar('id', $id);

        if (!acym_isAdmin()) {
            $hiddenlistsString = acym_getVar('string', 'hiddenlists', '');
            if (!empty($hiddenlistsString)) {
                $hiddenlists = explode(',', $hiddenlistsString);
                acym_arrayToInteger($hiddenlists);
                foreach ($hiddenlists as $oneListId) {
                    $formData['listsub'][$oneListId] = ['status' => 1];
                }
            }
        }

        if (empty($formData['listsub'])) {
            return true;
        }

        if (!$allowSubscriptionModifications) {
            $this->requireId = true;
            $this->errors[] = acym_translation('ACYM_NOT_ALLOWED_MODIFY_USER');

            return false;
        }

        $addLists = [];
        $unsubLists = [];
        foreach ($formData['listsub'] as $listID => $oneList) {
            if ($oneList['status'] == 1) {
                $addLists[] = $listID;
            } else {
                $unsubLists[] = $listID;
            }
        }

        $this->subscribe($id, $addLists);
        if (!$this->newUser) {
            $this->unsubscribe($id, $unsubLists);
        }

        return true;
    }

    public function sendConfirmation($userID)
    {
        if (!$this->forceConf && !$this->sendConf) return true;

        $config = acym_config();
        if ($config->get('require_confirmation', 1) != 1 || acym_isAdmin()) return false;

        $myuser = $this->getOneById($userID);

        if (!empty($myuser->confirmed)) return false;

        $mailerHelper = acym_get('helper.mailer');

        $mailerHelper->checkConfirmField = false;
        $mailerHelper->checkEnabled = false;
        $mailerHelper->report = $config->get('confirm_message', 0);

        $alias = "acy_confirm";

        $this->confirmationSentSuccess = $mailerHelper->sendOne($alias, $myuser);
        $this->confirmationSentError = $mailerHelper->reportMessage;
    }

    public function deactivate($userId)
    {
        acym_query('UPDATE `#__acym_user` SET `active` = 0 WHERE `id` = '.intval($userId));
    }

    public function confirm($userId)
    {
        $confirmDate = date('Y-m-d H:i:s', time());
        $ip = acym_getIP();
        $query = 'UPDATE `#__acym_user`';
        $query .= ' SET `confirmed` = 1, `confirmation_date` = '.acym_escapeDB($confirmDate).', `confirmation_ip` = '.acym_escapeDB($ip);
        $query .= ' WHERE `id` = '.intval($userId).' LIMIT 1';
        $res = acym_query($query);
        if ($res === false) {
            $msg = 'Please contact the admin of this website with the error message:<br />'.substr(strip_tags(acym_getDBError()), 0, 200).'...';
            acym_display($msg, 'error');
            exit;
        }


        $historyClass = acym_get('class.history');
        $historyClass->insert($userId, 'confirmed');

        $listIDs = acym_loadResultArray('SELECT `list_id` FROM `#__acym_user_has_list` WHERE `status` = 1 AND `user_id` = '.intval($userId));

        if (empty($listIDs)) {
            return;
        }

        $listClass = acym_get('class.list');
        $listClass->sendWelcome($userId, $listIDs);
    }

    public function getOneByIdWithCustomFields($id)
    {
        $user = $this->getOneById($id);
        $user = get_object_vars($user);

        $fieldsValue = acym_loadObjectList(
            'SELECT user_field.value as value, field.name as name 
            FROM #__acym_user_has_field as user_field 
            LEFT JOIN #__acym_field as field ON user_field.field_id = field.id 
            WHERE user_field.user_id = '.intval($id),
            'name'
        );

        foreach ($fieldsValue as $key => $value) {
            $fieldsValue[$key] = $value->value;
        }

        return array_merge($user, $fieldsValue);
    }

    public function getAllColumnsUserAndCustomField($inAction = false)
    {
        $return = [];

        $userFields = acym_getColumns('user');
        foreach ($userFields as $value) {
            $return[$value] = $value;
        }

        $customFields = acym_loadObjectList('SELECT * FROM #__acym_field WHERE id NOT IN (1, 2) '.($inAction ? 'AND type != "phone"' : ''), 'id');
        if (!empty($customFields)) {
            foreach ($customFields as $key => $value) {
                $return[$key] = $value->name;
            }
        }

        return $return;
    }

    public function getAllUserFields($user)
    {
        if (empty($user->id)) return $user;
        $query = 'SELECT field.*, userfield.* 
                    FROM #__acym_field AS field 
                    LEFT JOIN #__acym_user_has_field AS userfield ON field.id = userfield.field_id AND userfield.user_id = '.intval($user->id).' 
                    WHERE field.id NOT IN(1, 2)';

        $allFields = acym_loadObjectList($query);

        foreach ($allFields as $oneField) {
            $user->{$oneField->namekey} = empty($oneField->value) ? '' : $oneField->value;
        }

        return $user;
    }

    public function synchSaveCmsUser($user, $isnew, $oldUser = null)
    {
        $source = acym_getVar('cmd', 'acy_source', '');
        if (empty($source)) acym_setVar('acy_source', ACYM_CMS);

        $config = acym_config();

        if (!$config->get('regacy', 0)) return;

        $this->checkVisitor = false;
        $this->sendConf = false;

        $cmsUser = new stdClass();
        $cmsUser->email = trim(strip_tags($user['email']));
        if (!acym_isValidEmail($cmsUser->email)) return;
        if (!empty($user['name'])) $cmsUser->name = trim(strip_tags($user['name']));
        if (!$config->get('regacy_forceconf', 0)) $cmsUser->confirmed = 1;
        $cmsUser->active = 1 - intval($user['block']);
        $cmsUser->cms_id = $user['id'];

        if (!$isnew && !empty($oldUser['email']) && $user['email'] != $oldUser['email']) {
            $acyUser = $this->getOneByEmail($oldUser['email']);
            if (!empty($acyUser)) $cmsUser->id = $acyUser->id;
        }

        if (empty($cmsUser->id) && !empty($cmsUser->cms_id)) {
            $acyUser = $this->getOneByCMSId($cmsUser->cms_id);
            if (!empty($acyUser)) $cmsUser->id = $acyUser->id;
        }

        $acyUser = $this->getOneByEmail($cmsUser->email);
        if (!empty($acyUser)) {
            if (empty($cmsUser->id)) {
                $cmsUser->id = $acyUser->id;
            } elseif ($cmsUser->id != $acyUser->id) {
                $this->delete($acyUser->id);
            }
        }

        $isnew = (bool)($isnew || empty($cmsUser->id));

        $id = $this->save($cmsUser);



        $currentSubscription = $this->getSubscriptionStatus($id);

        $autoLists = $isnew ? $config->get('regacy_autolists') : '';
        $autoLists = explode(',', $autoLists);
        acym_arrayToInteger($autoLists);

        $listsClass = acym_get('class.list');
        $allLists = $listsClass->getAll();

        $visibleLists = acym_getVar('string', 'regacy_visible_lists');
        $visibleLists = explode(',', $visibleLists);
        acym_arrayToInteger($visibleLists);

        $visibleListsChecked = acym_getVar('array', 'regacy_visible_lists_checked', []);
        acym_arrayToInteger($visibleListsChecked);


        if (!$isnew && !empty($visibleLists)) {
            $currentlySubscribedLists = [];
            foreach ($currentSubscription as $oneSubscription) {
                if ($oneSubscription->status == 1) $currentlySubscribedLists[] = $oneSubscription->list_id;
            }
            $unsubscribeLists = array_intersect($currentlySubscribedLists, array_diff($visibleLists, $visibleListsChecked));
            $this->unsubscribe($id, $unsubscribeLists);
        }

        $listsToSubscribe = [];
        foreach ($allLists as $oneList) {
            if (!$oneList->active) continue;
            if (!empty($currentSubscription[$oneList->id]) && $currentSubscription[$oneList->id]->status == 1) continue;

            if (in_array($oneList->id, $visibleListsChecked) || (in_array($oneList->id, $autoLists) && !in_array($oneList->id, $visibleLists) && empty($currentSubscription[$oneList->id]))) {
                $listsToSubscribe[] = $oneList->id;
            }
        }

        if (!empty($listsToSubscribe)) $this->subscribe($id, $listsToSubscribe);

        $acymailingUser = $this->getOneById($id);
        if (!$config->get('regacy_forceconf', 0) || !empty($user['block']) || !empty($acymailingUser->confirmed)) return;


        if ($isnew || !empty($oldUser['block'])) {
            $this->forceConf = true;
            $this->sendConfirmation($id);
        }
    }

    public function synchDeleteCmsUser($userEmail)
    {
        $acyUser = $this->getOneByEmail($userEmail);

        if (empty($acyUser)) return;

        $config = acym_config();
        if ($config->get('regacy', '0') == 1 && $config->get('regacy_delete', '0') == 1) {
            $this->delete($acyUser->id);
        } else {
            acym_query('UPDATE #__acym_user SET `cms_id` = 0 WHERE `id` = '.intval($acyUser->id));
        }
    }

    public function getUsersLikeEmail($pattern)
    {
        $query = 'SELECT id, email FROM #__acym_user WHERE email LIKE '.acym_escapeDB('%'.$pattern.'%');
        $res = acym_loadObjectList($query);

        return $res;
    }
}

