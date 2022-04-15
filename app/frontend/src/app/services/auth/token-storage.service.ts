import { Injectable } from '@angular/core';

const TOKEN_KEY = 'token';

@Injectable({
  providedIn: 'root'
})
export class TokenStorageService
{
  constructor() {
  }

  signOut(): void
  {
    window.sessionStorage.removeItem(TOKEN_KEY)
  }

  public saveToken(token: Token): void
  {
    window.sessionStorage.removeItem(TOKEN_KEY)
    window.sessionStorage.setItem(TOKEN_KEY, JSON.stringify(token))
  }

  public getToken(): Token | null
  {
    let token = window.sessionStorage.getItem(TOKEN_KEY)

    if (token) {
      return Token.fromRawToken(JSON.parse(token))
    }

    return null
  }

  public getUser(): UserPayload | any
  {
    const token = this.getToken()

    if (token) {
      return token.getUser()
    }

    return {}
  }
}

export class Token
{
  constructor(
    private user: UserPayload,
    private token: string
  ) {
  }

  public getUser()
  {
    return this.user
  }

  public getToken()
  {
    return this.token
  }

  public static fromRawToken(token: any)
  {
    return new Token(
      new UserPayload(
        token.user.name,
        token.user.email,
        token.user.roles
      ),
      token.token
    )
  }
}

export class UserPayload
{
  constructor(
    private name: string,
    private email: string,
    private roles: {}
  ) {
  }

  public getName(): string
  {
    return this.name
  }

  public getEmail(): string
  {
    return this.email
  }

  public getRoles(): {}
  {
    return this.roles
  }
}
