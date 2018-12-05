<template>
    <div class="container-fluid">
        <div class="row mt-5">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" :src="getAvatarProfile()" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"> @{{this.form.name}} </h3>

                <p class="text-muted text-center">Software Engineer</p>

                <!-- <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Followers</b> <a class="float-right">1,322</a>
                  </li>
                  <li class="list-group-item">
                    <b>Following</b> <a class="float-right">543</a>
                  </li>
                  <li class="list-group-item">
                    <b>Friends</b> <a class="float-right">13,287</a>
                  </li>
                </ul> -->

                <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">About Me</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- <strong><i class="fa fa-book mr-1"></i> Education</strong>

                <p class="text-muted">
                  B.S. in Computer Science from the University of Tennessee at Knoxville
                </p>

                <hr>

                <strong><i class="fa fa-map-marker mr-1"></i> Location</strong>

                <p class="text-muted">Malibu, California</p>

                <hr>

                <strong><i class="fa fa-pencil mr-1"></i> Skills</strong>

                <p class="text-muted">
                  <span class="tag tag-danger">UI Design</span>
                  <span class="tag tag-success">Coding</span>
                  <span class="tag tag-info">Javascript</span>
                  <span class="tag tag-warning">PHP</span>
                  <span class="tag tag-primary">Node.js</span>
                </p>

                <hr> -->

                <strong><i class="fas fa-file-word mr-1"></i>Notes</strong>

                <p class="text-muted">{{this.form.bio}}</p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">

                  <li class="nav-item"><a class="nav-link active show" href="#settings" data-toggle="tab">Profile</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">


                  <div class="tab-pane active show" id="settings">
                    <form class="form-horizontal" >
                      <div class="form-group">
                        <label for="inputName" class="col-sm-2 control-label">Name</label>

                        <div class="col-sm-7">
                          <input type="text" class="form-control" :class="{'is-invalid':form.errors.has('name')}" id="inputName" placeholder="Name" v-model="form.name">
                          <has-error :form="form" field ="name"></has-error>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                        <div class="col-sm-7">
                          <input type="email" class="form-control" v-model="form.email" :class="{'is-invalid':form.errors.has('email')}" id="inputEmail" placeholder="Email">
                          <has-error :form="form" field ="email"></has-error>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="inputBio" class="col-sm-2 control-label">Biography</label>

                        <div class="col-sm-10">
                          <textarea class="form-control" id="inputBio" placeholder="Biography" v-model="form.bio" :class="{'is-invalid':form.errors.has('bio')}"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputSkills" class="col-sm-2 control-label">Profile Photo</label>

                        <div class="col-sm-10">
                          <input type="file" class="form-input" @change="updatePhoto" name="avatar" :class="{'is-invalid':form.errors.has('avatar')}">
                          <has-error :form="form" field ="avatar"></has-error>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="inputSkills" class="col-sm-10 control-label">Password (leave empty if not change) </label>

                        <div class="col-sm-7">
                          <input type="password" class="form-control" v-model="form.password" id="password" :class="{'is-invalid':form.errors.has('password')}" autocomplete="current-password">
                          <has-error :form="form" field ="password"></has-error>
                        </div>
                      </div>


                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <button @click.prevent="updateInfo" type="submit" class="btn btn-danger">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
</template>

<script>
    export default {
        data(){
          return {
             form: new Form({
              id:'',
              name:'',
              email:'',
              password:'',
              bio:'',
              type:'',
              avatar:''
            })
          }
        },
        mounted() {
            //console.log('Component mounted.')
        },
        methods:{
          getAvatarProfile(){
            let avatar = (this.form.avatar.length > 200)? this.form.avatar : this.form.avatar
            return avatar

          },
          getNameProfile(){
            return this.form.name
          },
          updateInfo(){
            this.$Progress.start();
            if(this.form.password== ''){
              this.form.password = undefined
            }
            this.form.put('api/profile')
            .then(()=>{
               Fire.$emit('AfterAddOrModif')
              this.$Progress.finish();
            })
            .catch(()=>{

              this.$Progress.fail();
            })
          },
          updatePhoto(e){
            let file = e.target.files[0]
            var reader = new FileReader();
            if(file['size']< 2111775){
              reader.onloadend = (file)=> {
                //console.log('RESULT', reader.result)
                this.form.avatar = reader.result
              }
              reader.readAsDataURL(file);
            }else{
              swal({
                type: "error",
                title: "Oops ...!",
                text: "You are uploading a large file"
              })
            }

          },
          chargeProfile(){
            axios.get('api/profile').then(({data})=>(this.form.fill(data)))


          }
        },
        created(){
          axios.get('api/profile').then(({data})=>(this.form.fill(data)))
          Fire.$on('AfterAddOrModif',()=>{
            this.chargeProfile();
          })
        }



    }
</script>
