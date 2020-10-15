import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  providers: [UserService]
})
export class RegisterComponent implements OnInit {
  public page_title : string;
  public user:User;
  public status:string;
  constructor(
    private _UserService: UserService
  ) {
    this.page_title = 'Registrate';
    this.user = new User(1,'','','ROLE_USER','','','','');
   }

  ngOnInit(): void {
    console.log('componentes de registro lanzado');
    console.log(this._UserService.test());
  }

  onSubmit(form){
    this._UserService.register(this.user).subscribe(
      response => {
        if(response.status == "success"){
          this.status = response.status;
          form.reset();
        }else{
          this.status = 'error';
        }
         
      },
      error => {
        console.log(<any>error);
      }
    );
    
   
  }

}
