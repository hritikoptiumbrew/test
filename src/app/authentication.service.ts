import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';

@Injectable()
export class AuthenticationService implements CanActivate {

  constructor(private router: Router) {
    console.log(window.location.href);
  }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    
    if (localStorage.getItem('photoArtsAdminToken')) {
      // if logged in, then return true
      // var urlstring = window.location.href;

      return true;
    }
    // if not logged in, then redirect to login page with the return url and return false
    this.router.navigate(['/admin']);
    return false;
  }

}
