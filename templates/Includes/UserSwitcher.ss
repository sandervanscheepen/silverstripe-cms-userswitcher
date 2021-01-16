CanUserSwitch: {$canUserSwitch()}
<% if $canUserSwitch  %>
    <div class="cms-subsites cms-userswitcher" data-pjax-fragment="CMSUserSwitcherMemberList">
        <div class="field dropdown">
            <select id="UserSwitcherSelect">
                <% loop $SwitchableMembers %>
                    <option value="$MemberID" $CurrentState>$Title</option>
                <% end_loop %>
            </select>
        </div>
    </div>
<% end_if %>
