<header class="navbar navbar-fixed-top bs-docs-nav" id="top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/DuoQ/" class="navbar-brand">DuoQ</a>
        </div>
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">
                <!-- check if the user is loged -->
                <!-- if yes the logout menu appear -->
                <?php if ($_SESSION['loggedUserObject']) { ?>
                    <li class="dropdown">
                        <a href="" class="dropdown-toggle" data-toggle="dropdown">Lanes <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?l=stats">Browse statistics</a></li>
                            <li><a href="index.php?l=duo">New duo lane</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li><a href="index.php?l=login">Lanes</a></li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <!-- check if the user is loged -->
                <!-- if yes the logout menu appear -->
                <?php if ($_SESSION['loggedUserObject']) { ?>
                    <li class="dropdown">
                        <a href="index.php?l=login" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> Account <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?l=mySum">Game account</a></li>
                            <li><a href="index.php?l=Settings">Settings</a></li>
                            <li><a href="index.php?l=logout">Logout</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li><a href="index.php?l=login"><span class="glyphicon glyphicon-user"></span> Account</a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</header>
<div class="imageContener">
    <div class="headerText">
        <h1>Duo Queue</h1>
        <p class="hidden-xs">Advanced Duo queue management</p>
    </div>
</div>