import { Injectable } from "@angular/core";

@Injectable({providedIn: 'root'})
export class Logger {

  log(data: any) {
    console.log(data)
  }

}
