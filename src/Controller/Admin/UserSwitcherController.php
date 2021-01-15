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
	use function in_array;
	
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
			$oSession = Controller::curr()->getRequest()->getSession();
			
			return ($oSession->get('UserSwitched') || Permission::check('ADMIN'));
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
							// UserSwitched wordt gebruikt om te checken of je mag switchen.. ook als je de ander bent geworden
							// zie
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
			$dlMembersThatCanBeImpersonated = Member::get()->filter([
				'CanBeImpersonatedByAdmin' => true
			]);
			
			$aMemberIDs = $dlMembersThatCanBeImpersonated->column('ID');
			
			/** @var Group $oAdminGroup */
			$oAdminGroup = Group::get()->filter([
				'Code' => 'administrators'
			])->first();
			
			if($oAdminGroup instanceof Group) {
				/** @var Member|CMSUserSwitcherMemberExt $oMember */
				foreach ($oAdminGroup->Members() as $oMember) {
					if ( ! in_array(intval($oMember->ID), $aMemberIDs)) {
						$aMemberIDs[] = intval($oMember->ID);
					}
				}
			}
			
			return Member::get()->filter([
				'ID' => $aMemberIDs
			])->sort('FirstName ASC, Surname ASC');
		}
	}