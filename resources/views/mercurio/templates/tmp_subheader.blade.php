<div class="col-12 p-2 px-3">
    <ul class="nav nav-pills solicitud-subheader-nav mb-1 flex-column flex-md-row gap-2" role="tablist">
        <%
        _.each(items, function(item){ 
        %>
        <li class="nav-item <%=(item.active)? 'active':'' %> <%=(item.hidden)? 'd-none':'' %>">
            <% if (item.tab !== '') { %>
            <a class="nav-link w-100 <%=(item.active)? 'active':'' %>"
                id="<%=item.id%>" data-bs-toggle="<%=(item.tab)? 'pill':''%>" href="<%='#'+item.tab %>" aria-controls="<%=item.tab %>" aria-selected='true'>
                <i class='<%=item.icon%>'></i> <%=item.label%>
            </a>
            <% }else { %>
            <a href="#" type="button" class="nav-link w-100 <%=(item.active)? 'active':'' %>" id="<%=item.id%>">
                <i class='<%=item.icon%>'></i> <%=item.label%>
            </a>
            <% } %>
        </li>
        <% })
        %>
    </ul>
</div>