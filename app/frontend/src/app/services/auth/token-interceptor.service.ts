import { HTTP_INTERCEPTORS, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { TokenStorageService } from "./token-storage.service";

const TOKEN_HEADER_KEY = 'Authorization';

@Injectable()
export class TokenInterceptorService implements HttpInterceptor
{
  constructor(private tokenStorage: TokenStorageService) {
  }

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    let authReq = req;
    const token = this.tokenStorage.getToken();

    if (token != null) {
      authReq = req.clone({headers: req.headers.set(TOKEN_HEADER_KEY, 'Bearer ' + token.getToken())});
    }

    return next.handle(authReq);
  }
}

export const tokenInterceptorProviders = [
  {provide: HTTP_INTERCEPTORS, useClass: TokenInterceptorService, multi: true}
];
