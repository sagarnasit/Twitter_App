<style>
.navbar-inverse .navbar-nav > li > a{
    color:white;
}
.twitter-icon{
    font-size: 40px;
    margin-top:-8px;
}
.my-input{
    /*border:1px solid white !important;*/
    border-radius:3px !important;
    padding-left:8px !important;
    background-color:#6bb4e2 !important;
    color:white !important;
}
::-webkit-input-placeholder { /* Chrome/Opera/Safari */
  color: white !important;
  font-size: 15px !important;
  margin-left: 8px !important;

}
.my-btn{
    padding: 6px;
    color: white;
    /*background: #528ae5;*/
}
</style>

<!-- Bootstrap navbar -->
<nav class="navbar navbar-inverse" >
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <i class="navbar-brand fa fa-twitter twitter-icon" style="color:white;"></i>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">

                <li><a href="/home">Home</a></li>
                <li class=""><a href="" data-toggle="modal" data-target="#myPostTweetModal">Post Tweet</a></li>


            </ul>

            <form class="navbar-form navbar-left" action="/search" method="post" >
                <div class="form-group">
                    {{ csrf_field() }}
                    <input type="text" name="searchtext"class="form-control my-input" placeholder="Search Public Users" required
                    style="">
                </div>
                <button type="submit" name="button" class="btn btn-primary my-btn">Search</button>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <li><a >{{ Auth::user()->name }}</a></li>
                <li class=""><a href="/logout">Logout</a></li>

            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
