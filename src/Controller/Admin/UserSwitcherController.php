<?php

    namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Controller\Admin;

    use SanderVanScheepen\SilverstripeCMSUserSwitcher\Extension\CMSUserSwitcherMemberExt;
    use SilverStripe\Admin\LeftAndMain;
    use SilverStripe\Control\Controller;
    use SilverStripe\Core\Injector\Injector;
    use SilverStripe\Security\Group;
    use SilverStripe\Security\IdentityStore;
    use SilverStripe\Security\Member;
    use SilverStripe\Security\Permission;
    use SilverStripe\Security\Security;
    use function in_array;
    use function intval;

    class UserSwitcherController extends LeftAndMain
    {
        private static $url_segment = 'userswitcher_xhr';
        private static $url_rule = '/$Action/$ID/$OtherID';
        private static $menu_icon_class = 'font-icon-cross-mark';
        private static $required_permission_codes = false;

        //private static $subitem_class = 'Member';

        private static $url_handlers = [
            '' => 'index',
        ];

        private static $allowed_actions = [];

        public function canView($member = null)
        {
            return true;
        }

        public function canUserSwitch()
        {
            /** @var Member $oCurrentMember */
            $oCurrentMember   = Security::getCurrentUser();

            $oSession = Controller::curr()->getRequest()->getSession();

            return (
                $oSession->get('UserSwitched')
                || (Permission::check('ADMIN') && in_array($oCurrentMember->CMSUserSwitchCanSwitch, [true, 1, '1']))
            );
        }

        public function index($request)
        {
            // admin/userswitcher_xhr?UserSwitcherMemberID=2&BackURL=
            $sInputMemberID = $request->requestVar('UserSwitcherMemberID');

            if ($this->canUserSwitch() === true) {

                if ($sInputMemberID !== null) {

                    $dlMembers  = UserSwitcherController::getSwitchableMembers();
                    $aMemberIDs = $dlMembers->column('ID');

                    if (in_array(intval($sInputMemberID), $aMemberIDs)) {

                        $oMember = Member::get()->byID($sInputMemberID);
                        if ($oMember) {
                            $this->getRequest()->getSession()->set('UserSwitched', 1);
                            $oIdentityStore = Injector::inst()->get(IdentityStore::class);
                            $oIdentityStore->logIn($oMember, false, $this->getRequest());

                            return $this->redirectBack();
                        }
                    }
                    else {
                        return $this->redirectBack();
                    }
                }
            }

            return $this->getResponseNegotiator()->respond($request);
        }

        public static function getSwitchableMembers()
        {
            /** @var Member $oCurrentMember */
            $oCurrentMember   = Security::getCurrentUser();
            $iCurrentMemberID = intval($oCurrentMember->ID);

            $dlMembersThatCanBeImpersonated = Member::get()->filter([
                'CMSUserSwitchCanBeImpersonatedByAdmin' => true
            ]);

            $aMemberIDs = $dlMembersThatCanBeImpersonated->column('ID');

            if (in_array($iCurrentMemberID, $aMemberIDs) !== true) {
                $aMemberIDs[] = $iCurrentMemberID;
            }

            return Member::get()->filter([
                'ID' => $aMemberIDs
            ])->sort('FirstName ASC, Surname ASC');
        }
    }
