import { Component, OnInit } from '@angular/core';
import { Token, TokenStorageService, UserPayload } from "../../services/auth/token-storage.service";

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit
{
  token: Token|null;
  user: UserPayload|null;

  constructor(
    private tokenStorage: TokenStorageService
  ) {
    this.token = tokenStorage.getToken()
    this.user = tokenStorage.getUser()
  }

  ngOnInit(): void {
    // this.user = this.tokenStorage.getUser();
  }
}
