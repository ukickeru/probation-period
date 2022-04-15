import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

const AUTH_API = 'https://localhost:8091/api';
const httpOptions = {
  headers: new HttpHeaders({'Content-Type': 'application/json'})
};

@Injectable({
  providedIn: 'root'
})
export class AuthService
{
  constructor(private http: HttpClient) {
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
