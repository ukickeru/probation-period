import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from "./pages/home/home.component";
import { LoginComponent } from "./pages/login/login.component";
import { RegistrationComponent } from "./pages/registration/registration.component";
import { ProfileComponent } from "./pages/profile/profile.component";
import { AuthService } from "./services/auth/auth.service";

export const HOME_PATH = '';
export const LOGIN_PATH = 'login';
export const REGISTRATION_PATH = 'registration';
export const LOGOUT_PATH = 'logout';

const routes: Routes = [
  { path: LOGIN_PATH, component: LoginComponent },
  { path: REGISTRATION_PATH, component: RegistrationComponent },
  { path: LOGOUT_PATH, redirectTo: LOGIN_PATH },
  { path: HOME_PATH, component: HomeComponent, canActivate: [AuthService] },
  { path: 'profile', component: ProfileComponent, canActivate: [AuthService] },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule
{
}
