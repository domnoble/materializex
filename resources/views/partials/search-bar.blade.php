<nav class="{{ get_option( 'mx_search_bar_class' ) }}">
   <div class="nav-wrapper">
     <form role="search" method="get" class="search-form" action="{{home_url( '/' )}}">
       <div class="input-field">
         <input id="search" type="search" name="s" required>
         <label class="label-icon" for="search"><i class="material-icons">search</i></label>
         <i class="material-icons">close</i>
       </div>
     </form>
   </div>
 </nav>
