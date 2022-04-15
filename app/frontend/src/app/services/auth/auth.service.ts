import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot } from "@angular/router";
import { Token, TokenStorageService } from "./token-storage.service";
import { AppRoutingModule, HOME_PATH, LOGIN_PATH, REGISTRATION_PATH } from "../../app-routing.module";

const AUTH_API = 'https://localhost:8091/api';
const httpOptions = {
  headers: new HttpHeaders({'Content-Type': 'application/json'})
};

@Injectable({
  providedIn: 'root'
})
export class AuthService implements CanActivate
{
  constructor(
    private http: HttpClient,
    private tokenStorage: TokenStorageService,
    private router: Router
  ) {
  }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot)
  {
    if (this.tokenStorage.getToken() === null) {
      return this.router.parseUrl(LOGIN_PATH)
    }

    if (state.url === LOGIN_PATH || state.url === REGISTRATION_PATH) {
      return this.router.parseUrl(HOME_PATH)
    }

    return true;
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post(
      AUTH_API + '/login',
      {
        email,
        password
      },
      httpOptions
    );
  }

  register(email: string, name: string, password: string): Observable<any> {
    return this.http.post(
      AUTH_API + '/registration',
      {
        email,
        name,
        password
      },
      httpOptions
    );
  }
}
