
//require [converse];

function InitXmpp()
{
    converse.initialize({
            bosh_service_url: 'https://bind.conversejs.org', // Please use this connection manager only for testing purposes
            i18n: locales.en, // Refer to ./locale/locales.js to see which locales are supported
            show_controlbox_by_default: true,
            roster_groups: true
        });
}