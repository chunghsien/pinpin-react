import loadable from '@loadable/component';

const DefaultContainer = loadable(() => import('./views/admin/pages/defaults/DefaultContainer'));
const pageConfig = window.pageConfig;
let _routes = [/*{ path: '/admin/', exact: true, strict: true, name: 'Home' }*/];

const lang = document.getElementsByTagName('html')[0].lang;
pageConfig.routes.map((item) => {
  let _component = null;
  if (item.component) {
    _component = loadable(() => import(item.component + '.js'));
  } else {
    if (item.uri != '/' + lang + '/admin/') {
      _component = DefaultContainer;
    }
  }
  let path = item.uri.replace(/\/\:method_or_id$/, '');
  const { basePath } = pageConfig;
  let _push = {
    path: (basePath + path).replace(/^\/{2,}/, '/'),
    name: item.name,
  };
  if (item.uri != '/' + lang + '/admin/') {
    _push.component = _component;
    //_push.strict = true;
  } else {
    _push.exact = true;
  }
  _routes.push(_push);
});
const routes = _routes;
export default routes;