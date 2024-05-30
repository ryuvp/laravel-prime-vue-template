import {login, logout, getInfo} from '@/app/api/auth'
import {isLogged, setToken, removeToken} from '@/app/utils/auth'
import router, {resetRouter} from '@/app/router'
import {defineStore} from "pinia"
import {permissionStore} from "./permission";

export const userStore = defineStore('user', {
  state: () => {
    return {
      id: null,
      user: null,
      token: null,
      name: '',
      roles: [],
      permissions: [],
      routes: [],
    }
  },
  actions: {
    // user login
    login(userInfo) {
      const {email, password} = userInfo
      return new Promise((resolve, reject) => {
        login({email: email.trim(), password: password})
          .then(response => {
            setToken(response.token)
            resolve()
          })
          .catch(error => {
            console.log(error)
            reject(error)
          })
      })
    },
    // get user info
    getInfo() {
      return new Promise((resolve, reject) => {
        getInfo()
          .then(response => {
            const {data} = response

            if (!data) {
              reject('Verification failed, please Login again.')
            }

            const {roles, name, permissions, id} = data

            if (!roles || roles.length <= 0) {
              reject('getInfo: roles must be a non-null array!')
            }

            const hierarchicalPermissions = this.transformPermissions(permissions);

            this.$patch((state) => {
              state.id = id
              state.name = name
              state.roles = roles
              state.permissions = hierarchicalPermissions
              state.routes = permissions
            })
            resolve(data)
          })
          .catch(error => {
            console.log(error)
            reject(error)
          })
      })
    },
    // user logout
    logout() {
      return new Promise((resolve, reject) => {
        logout()
          .then(() => {
            this.$patch((state) => {
              state.token = ''
              state.roles = []
            })
            removeToken()
            resetRouter()
            resolve()
          })
          .catch(error => {
            reject(error)
          })
      })
    },
    // remove token
    resetToken() {
      return new Promise(resolve => {
        this.$patch((state) => {
          state.token = ''
          state.roles = []
        })
        removeToken()
        resolve()
      })
    },
    // Dynamically modify permissions
    changeRoles(role) {
      return new Promise(async resolve => {
        const roles = [role.name]
        const permissions = role.permissions.map(permission => permission.name)
        this.$patch((state) => {
          state.permissions = permissions
          state.roles = roles
        })
        resetRouter()

        // generate accessible routes map based on roles

        const usePermissionStore = permissionStore()
        const accessRoutes = await usePermissionStore.generateRoutes(roles, permissions)

        // dynamically add accessible routes
        accessRoutes.forEach((item) => {
          router.addRoute(item)
        })

        resolve()
      })
    },

    transformPermissions(permissions) {
      const permissionMap = {};
      permissions.forEach(permission => {
        const levels = permission.name.split('.');
        let currentLevel = permissionMap;
        levels.forEach((level, index) => {
          if (!currentLevel[level]) {
            currentLevel[level] = {
              ...permission,
              children: {}
            };
          }
          if (index === levels.length - 1) {
            currentLevel[level] = { ...permission, children: {} };
          }
          currentLevel = currentLevel[level].children;
        });
      });

      function buildHierarchy(map) {
        return Object.keys(map).map(key => {
          if (Object.keys(map[key].children).length === 0) {
            delete map[key].children;
          } else {
            map[key].children = buildHierarchy(map[key].children);
          }
          return map[key];
        });
      }
      return buildHierarchy(permissionMap);
    },
  },
})
