<template>
    <div>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">

                  <div v-if="this.$window.userName !== undefined">
                    <a href="/logout">
                      <button type="button" class="navbar-toggle fas fa-sign-out-alt">
                      </button>
                    </a>
                    <label class="navbar-toggle mobile-user-name">
                      {{ this.$window.userName }}
                    </label>
                  </div>
                  <div v-else>
                    <a href="/login" class="navbar-toggle fas">
                      {{ 'Войти: ' }}
                      <button type="button" class="fas fa-sign-in-alt">
                      </button>
                    </a>
                  </div>

                  <a class="navbar-brand" href="#" v-if="(this.$route.name != 'home') &&
                        (this.$route.meta.label !== undefined)">
                        <router-link :to="{ name: 'home' }">Арбайт</router-link>
                        {{' / ' + this.$route.meta.label}}
                    </a>
                    <a class="navbar-brand" href="#" v-else>
                        <router-link :to="{ name: 'home' }">Арбайт</router-link>
                    </a>
                    <!--<ul class="nav navbar-nav" v-if="this.$route.name != 'home'">
                        <li><a>{{this.$route.meta.label}}</a></li>
                    </ul>-->

                </div>
                <div id="navbar-collapse-x" class="navbar-collapse collapse">
                  <ul class="nav navbar-nav navbar-right">
                        <li class="user-bar">
                          <div v-if="this.$window.userName !== undefined">
                            Пользователь: &nbsp;{{ this.$window.userName }}
                            &nbsp; <a href="/logout" class="fas fa-sign-out-alt"></a>
                          </div>
                          <div v-else>
                            <a href="/login">Войти: <i class="fas fa-sign-in-alt"></i></a>
                          </div>
                        </li>
                        <li>
                            <router-link :to="{ name: 'about' }">О нас</router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'faq' }">FAQ</router-link>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </nav>
        <div>
            <transition name="fade">
                <router-view></router-view>
            </transition>
        </div>
    </div>
</template>

<style lang="scss">

  .nav .user-bar {
    padding-top: 15px;
    padding-right: 15px;
  }

  .mobile-user-name {
    padding-top: 6px;
  }

  .my-label {
      font-size: 130%;
  }

  .fade-enter-active, .fade-leave-active {
      transition-property: opacity;
      transition-duration: .25s;
  }

  .fade-enter-active {
      transition-delay: .25s;
  }

  .fade-enter, .fade-leave-active {
      opacity: 0
  }
</style>

<script>
    export default {
        data: function () {
            return {
                display: true
            }
        },

    }


</script>