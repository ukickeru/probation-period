import { Component, OnInit } from '@angular/core';
import { AuthService } from "../../services/auth/auth.service";
import { Token, TokenStorageService, UserPayload } from "../../services/auth/token-storage.service";

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit
{
  form: any = {
    email: null,
    password: null
  };
  isLoggedIn = false;
  isLoginFailed = false;
  errorMessage = '';
  roles: string[] = [];

  constructor(private authService: AuthService, private tokenStorage: TokenStorageService) {
  }

  ngOnInit(): void {
    if (this.tokenStorage.getToken()) {
      this.isLoggedIn = true;
      this.roles = this.tokenStorage.getUser().roles;
    }
  }

  onSubmit(): void {
    const {email, password} = this.form;

    this.authService.login(email, password).subscribe(
      data => {

        let user = new UserPayload(
          data.user.name,
          data.user.email,
          data.user.roles
        )

        let token = data.token

        this.tokenStorage.saveToken(
          new Token(user, token)
        );

        this.isLoginFailed = false;
        this.isLoggedIn = true;
        this.roles = this.tokenStorage.getUser().getRoles();
        this.reloadPage();
      },
      err => {
        this.errorMessage = err.error.message;
        this.isLoginFailed = true;
      }
    );
  }

  reloadPage(): void {
    window.location.reload();
  }
}
