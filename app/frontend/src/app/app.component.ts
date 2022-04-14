import { Component } from '@angular/core';
import { Logger } from "../services/logger/logger.service";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {

  title = 'Probation';

  constructor(
    private logger: Logger
  ) {
  }

  onLogMe() {
    this.logger.log('Hello world from injected service! :)')
  }

}
