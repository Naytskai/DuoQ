<header>
    <a href="/DuoQ/">
        <button type="button" class="btn btn-default">
            Home
        </button>
    </a>
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            Lanes
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li><a href="index.php?l=stats">Browse statistics</a></li>
            <li><a href="index.php?l=duo">New duo lane</a></li>
        </ul>
    </div>
    <!-- check if the user is loged -->
    <?php if ($_SESSION['loggedUserObject']) { ?>
        <!-- if yes the logout menu appear -->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                Account
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="index.php?l=addSumm">Link game account</a></li>
                <li><a href="index.php?l=parameters">Parameters</a></li>
                <li><a href="index.php?l=logout">Logout</a></li>
            </ul>
        </div>
    <?php } else { ?>
        <!-- if no the account button appear -->
        <a href="index.php?l=login">
            <button type="button" class="btn btn-default">
                Account
            </button>
        </a>
    <?php } ?>
</header>