requirejs.config(
{
  enforceDefine: true,
  baseUrl: '/jvlm/',  
  paths:
  {
    "jquery": "external/jquery/jquery-3.2.1.min",
    "jquery.bootstrap": "external/boostrap-master/js/bootstrap.min",
    'pStrength': "external/PasswordStrength/jquery.pstrength-min.1.2",
    "moment": "external/moments/moment-with-locales.min",
    'jvlm':'dist/jvlm_main.min.js?v=@@JVLMVERSION@@',
    'jQueryUI': "external/jquery-ui/jquery-ui.min",
    'FooTable' :'external/footable-bootstrap/js/footable.min'
  },
  shim:
  {
    jquery:
    {
      exports: "jQuery"
    },
    jQueryUI:
    {
      deps: ["jquery"]
    },
    FooTable:
    {
      deps: ["jquery"]
    },
    "jquery.bootstrap":
    {
      deps: ["jquery"]
    },
    "jquery.verimail":
    {
      deps: ["jquery"]
    },
    pStrength:
    {
      deps: ["jquery"],
      exports: 'jQuery.pStrength'
    }
  }
});