<nav aria-label="breadcrumb" class="breadcrumb-nav px-3 py-2 mb-0">
    <ol class="breadcrumb mb-0 bg-transparent">
        <% _.each(breadcrumbs, function(crumb) { %>
        <li class="breadcrumb-item <%= crumb.is_active ? 'active' : '' %>"
            <%= crumb.is_active ? 'aria-current="page"' : '' %>>
            <% if (crumb.is_active) { %>
            <span><%= crumb.title %></span>
            <% } else { %>
            <a href="<%= crumb.url %>"><%= crumb.title %></a>
            <% } %>
        </li>
        <% }); %>
    </ol>
</nav>