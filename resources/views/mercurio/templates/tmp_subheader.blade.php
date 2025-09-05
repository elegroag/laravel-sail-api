<div class="col-auto p-2 pl-4">
    <ul class="nav nav-pills mb-1" role="tablist">
        <%
        _.each(items, function(item){ 
        %>
        <li class="nav-item <%=(item.active)? 'active':'' %> <%=(item.hidden)? 'd-none':'' %>">
            <% if (item.tab !== '') { %>
            <a class="nav-link <%=(item.active)? 'active':'' %>"
                id="<%=item.id%>" data-bs-toggle="<%=(item.tab)? 'pill':''%>" href="<%='#'+item.tab %>" aria-controls="<%=item.tab %>" aria-selected='true'>
                <i class='<%=item.icon%>'></i> <%=item.label%>
            </a>
            <% }else { %>
            <a href="#" type="button" class="nav-link <%=(item.active)? 'active':'' %>" id="<%=item.id%>">
                <i class='<%=item.icon%>'></i> <%=item.label%>
            </a>
            <% } %>
        </li>
        <% })
        %>
    </ul>
</div>