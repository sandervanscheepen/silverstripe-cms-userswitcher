<?php

namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Extension;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use function in_array;
use function intval;

class UserSwitcherDropdownLeftAndMainExt extends DataExtension
{

    public function init()
    {
        Requirements::javascript('sandervanscheepen/silverstripe-cms-userswitcher:client/dist/js/LeftAndMain_UserSwitcher.js');
        Requirements::css('sandervanscheepen/silverstripe-cms-userswitcher:client/dist/css/LeftAndMain_UserSwitcher.css');
    }

    public function SwitchableMembers()
    {
        $output = ArrayList::create();
        Requirements::javascript('sandervanscheepen/silverstripe-cms-userswitcher:client/dist/js/LeftAndMain_UserSwitcher.js');

        $dlMembers = UserSwitcherDropdownLeftAndMainExt::getSwitchableMembers();

        /** @var Member $oCurrentMember */
        $oCurrentMember = Security::getCurrentUser();

        /** @var Member $oMember */
        foreach ($dlMembers as $oMember) {
            $sCurrentState = intval($oMember->ID) === intval($oCurrentMember->ID) ? 'selected' : '';

            $sLabel = 'Member ' . $oMember->ID;
            $sName  = $oMember->getName();

            if ($sName && trim($sName) !== "") {
                $sLabel = $sName;
            }

            $output->push(ArrayData::create([
                'CurrentState' => $sCurrentState,
                'ID'           => $oMember->ID,
                'Title'        => $sLabel,
                'MemberID'     => $oMember->ID
            ]));
        }

        return $output;
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
        }

        return static::$oMemoizedCanUserSwitch;
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
}
