<% if $canUserSwitch  %>
    <div class="cms-subsites cms-userswitcher" data-pjax-fragment="CMSUserSwitcherMemberList">
        <div class="field dropdown" style="color: blue;">
            <select id="UserSwitcherSelect">
                <% loop $SwitchableMembers %>
                    <option value="$MemberID" $CurrentState>$Title</option>
                <% end_loop %>
            </select>
        </div>
    </div>
<% end_if %>
