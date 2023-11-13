<?= component(); ?>
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
        </tr>
    </thead>
    <tbody id="users_tbl" hx-get="api/getAllUser" hx-trigger="load">
        <tr><td>No Contents</td></tr>
    </tbody>
</table>