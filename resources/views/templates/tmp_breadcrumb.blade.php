<nav aria-label="breadcrumb" class="breadcrumb-nav bg-transparent px-3 py-2 mb-0 text-white">
    <ol class="breadcrumb mb-0">
        <% _.each(breadcrumbs, function(crumb) { %>
        <li class="breadcrumb-item <%= crumb.is_active ? 'active' : '' %>"
            <%= crumb.is_active ? 'aria-current="page"' : '' %>>
            <% if (crumb.is_active) { %>
            <%= crumb.title %>
            <% } else { %>
            <a href="<%= crumb.url %>"><%= crumb.title %></a>
            <% } %>
        </li>
        <% }); %>
    </ol>
</nav>