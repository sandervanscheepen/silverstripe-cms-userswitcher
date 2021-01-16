<?php

    namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Controller\Admin;

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
    use function serialize;
    use function var_dump;

    class UserSwitcherController extends LeftAndMain
    {
        private static $url_segment = 'cmsuserswitcher_xhr';
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

        public function index($request)
        {
            // admin/cmsuserswitcher_xhr?UserSwitcherMemberID=2&BackURL=
            $sInputMemberID = $request->requestVar('UserSwitcherMemberID');

            if ($this->canUserSwitch() === true) {

                if ($sInputMemberID !== null) {

                    $dlMembers  = UserSwitcherController::getSwitchableMembers();
                    $aMemberIDs = $dlMembers->column('ID');

                    if (in_array(intval($sInputMemberID), $aMemberIDs)) {

                        $oMember = Member::get()->byID($sInputMemberID);
                        if ($oMember) {
                            $this->getRequest()->getSession()->set('CMSUserSwitched', 1);
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

        protected static $oMemoizedSwitchableMembers = null;

        public static function getSwitchableMembers()
        {
            if (static::$oMemoizedSwitchableMembers === null) {
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

                static::$oMemoizedSwitchableMembers = Member::get()->filter([
                    'ID' => $aMemberIDs
                ])->sort('FirstName ASC, Surname ASC');
            }

            return static::$oMemoizedSwitchableMembers;
        }

        protected static $oMemoizedCanUserSwitch = null;

        public function canUserSwitch()
        {
            if (static::$oMemoizedCanUserSwitch === null) {
                /** @var Member $oCurrentMember */
                $oCurrentMember = Security::getCurrentUser();

                $oSession = Controller::curr()->getRequest()->getSession();

                static::$oMemoizedCanUserSwitch = (
                    $oSession->get('CMSUserSwitched')
                    || (Permission::check('ADMIN') && in_array($oCurrentMember->CMSUserSwitchCanSwitch, [true, 1, '1']) && static::getSwitchableMembers()->count() > 0)
                );

                static::$oMemoizedCanUserSwitch = serialize(static::$oMemoizedCanUserSwitch);
            }

            return static::$oMemoizedCanUserSwitch;
        }
    }
