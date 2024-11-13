window._ = require("lodash");
import SimpleBar from "simplebar";
window.SimpleBar = SimpleBar;

import notifier from "notifier-js";
window.notifier = notifier;

import "datatables.net-bs5";
import "datatables.net-responsive-bs5";

import "select2/dist/css/select2.min.css";
import "select2";

import { createPopper } from '@popperjs/core';
window.Popper = createPopper;

try {
    window.$ = window.jQuery = require("jquery");
    window.moment = require("moment-timezone");

    require("bootstrap");
    require("jquery-validation");
    require("jquery-ui-sortable");
    window.ProgressBar = require('progressbar.js');
    window.Swal = require("sweetalert2");
    window.moment = require("moment-timezone");
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
