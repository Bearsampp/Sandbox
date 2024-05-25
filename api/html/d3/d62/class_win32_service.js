/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

var class_win32_service =
[
    [ "__construct", "d3/d62/class_win32_service.html#a4717bbfc70a40a57ee741ed70766c309", null ],
    [ "callWin32Service", "d3/d62/class_win32_service.html#a1865b80a3371f2e75878a75090a66636", null ],
    [ "create", "d3/d62/class_win32_service.html#a435e7d7525d4bcd0ed5e34a469f3adf6", null ],
    [ "delete", "d3/d62/class_win32_service.html#a13bdffdd926f26b825ea57066334ff01", null ],
    [ "getBinPath", "d3/d62/class_win32_service.html#ac2fef71b0c424b00962dfea08c040a17", null ],
    [ "getDisplayName", "d3/d62/class_win32_service.html#a273f6220e7dea1423919139742e04902", null ],
    [ "getError", "d3/d62/class_win32_service.html#a24ada5decce3d1b79cd82f5a90ccf404", null ],
    [ "getErrorControl", "d3/d62/class_win32_service.html#a0c334edd51ffc159a2dcbffd7abde0ec", null ],
    [ "getLatestError", "d3/d62/class_win32_service.html#a92ef6862ad1c6bc4914e9ba6a9553d93", null ],
    [ "getLatestStatus", "d3/d62/class_win32_service.html#a7625ffd26a9939062a5be9d48a99a3b2", null ],
    [ "getName", "d3/d62/class_win32_service.html#a3d0963e68bb313b163a73f2803c64600", null ],
    [ "getNssm", "d3/d62/class_win32_service.html#aed19b7a2f2262c867efd08f641c59669", null ],
    [ "getParams", "d3/d62/class_win32_service.html#ae32cd7c32721b02d676bb63b4b1366db", null ],
    [ "getStartType", "d3/d62/class_win32_service.html#ae0f8bd6cdfde78e2371fd61a6ca4533c", null ],
    [ "getVbsKeys", "d3/d62/class_win32_service.html#a9a40b038fbfbb338744dedb087e78450", null ],
    [ "getWin32ErrorCodeDesc", "d3/d62/class_win32_service.html#af337ca4bec6663b0243091c245b7e73f", null ],
    [ "getWin32ServiceStatusDesc", "d3/d62/class_win32_service.html#a64d96656998371e18ecad89c7c231691", null ],
    [ "infos", "d3/d62/class_win32_service.html#aab614a27600f9ffa5e97f1d0ee30f490", null ],
    [ "isInstalled", "d3/d62/class_win32_service.html#aed5400933aebc8d6b364a68b19664d1f", null ],
    [ "isPaused", "d3/d62/class_win32_service.html#a753c75a39f0c5e861d1eb0083646d9d5", null ],
    [ "isPending", "d3/d62/class_win32_service.html#afdc2206fdf6e0d301fbfd03a070def15", null ],
    [ "isRunning", "d3/d62/class_win32_service.html#ad7e54aa55e82e9be1428d9fa8eb559a5", null ],
    [ "isStopped", "d3/d62/class_win32_service.html#aaf3350830f5be65c2a8521c0c937c73c", null ],
    [ "reset", "d3/d62/class_win32_service.html#a4a20559544fdf4dcb457e258dc976cf8", null ],
    [ "restart", "d3/d62/class_win32_service.html#ac8de9e38ce27c87f710dff42a13455cf", null ],
    [ "setBinPath", "d3/d62/class_win32_service.html#ae1bd1d4766547181f2b2341b734bdac0", null ],
    [ "setDisplayName", "d3/d62/class_win32_service.html#ac60edd848e67ad4f32d55a27798a2255", null ],
    [ "setErrorControl", "d3/d62/class_win32_service.html#a0de3bf7fb2e8218ad14383281d78183c", null ],
    [ "setName", "d3/d62/class_win32_service.html#a2fe666694997d047711d7653eca2f132", null ],
    [ "setNssm", "d3/d62/class_win32_service.html#a72c08f9bd09273817df3680d5fb3cf36", null ],
    [ "setParams", "d3/d62/class_win32_service.html#a99452a2ee9dfa3243a205c61d8f728cc", null ],
    [ "setStartType", "d3/d62/class_win32_service.html#ab00fa8ca0dfcf792ff77f0b07a93b593", null ],
    [ "start", "d3/d62/class_win32_service.html#af8fa59992209e36dccb3eefb0f75531f", null ],
    [ "status", "d3/d62/class_win32_service.html#a707975ef4ff1104de9ebc7b1c66a194e", null ],
    [ "stop", "d3/d62/class_win32_service.html#a8b6fc76a620d7557d06e9a11a9ffb509", null ],
    [ "writeLog", "d3/d62/class_win32_service.html#a2aaa55eaa3e3e023d5c372b46ba5099f", null ],
    [ "$binPath", "d3/d62/class_win32_service.html#a57f194ba56298871f05df43adc3fad15", null ],
    [ "$displayName", "d3/d62/class_win32_service.html#ac1b12cd5f2bbc3d7dcff91e4e27dfee6", null ],
    [ "$errorControl", "d3/d62/class_win32_service.html#aed5a5ae3c4ed003663d3b6dbf2c1747c", null ],
    [ "$latestError", "d3/d62/class_win32_service.html#a95b1c2c2027e643f614d735d1c1acd00", null ],
    [ "$latestStatus", "d3/d62/class_win32_service.html#a5be7d4fcfb54c8ac2e28a87c280ca9cb", null ],
    [ "$name", "d3/d62/class_win32_service.html#ab2fc40d43824ea3e1ce5d86dee0d763b", null ],
    [ "$nssm", "d3/d62/class_win32_service.html#a62cefc628556a180ca6c652d77474ea2", null ],
    [ "$params", "d3/d62/class_win32_service.html#afe68e6fbe7acfbffc0af0c84a1996466", null ],
    [ "$startType", "d3/d62/class_win32_service.html#abf1de419667e9f7fefc5105b1e0df3d5", null ],
    [ "PENDING_TIMEOUT", "d3/d62/class_win32_service.html#afb1e5546bc19c7e241593e3e53f3f40e", null ],
    [ "SERVER_ERROR_IGNORE", "d3/d62/class_win32_service.html#a3823888df68c3c58e386a12392d85fd3", null ],
    [ "SERVER_ERROR_NORMAL", "d3/d62/class_win32_service.html#a32e3c38d0e652a752f3bd21d2ffac6bb", null ],
    [ "SERVICE_AUTO_START", "d3/d62/class_win32_service.html#af85a1af914fe4bb7320ee3d82ad8ea41", null ],
    [ "SERVICE_DEMAND_START", "d3/d62/class_win32_service.html#af4b1c22a9b0805029cafd92ebc1307d2", null ],
    [ "SERVICE_DISABLED", "d3/d62/class_win32_service.html#a8fd875cdf8457f6272f9e7431abe5c2d", null ],
    [ "SLEEP_TIME", "d3/d62/class_win32_service.html#aff3603db3027215072b862544f660f77", null ],
    [ "VBS_DESCRIPTION", "d3/d62/class_win32_service.html#a621513c97bf72862ffbe7ebfd3d540bd", null ],
    [ "VBS_DISPLAY_NAME", "d3/d62/class_win32_service.html#a760f3a02a20bae2b64b40188767e1ad6", null ],
    [ "VBS_NAME", "d3/d62/class_win32_service.html#a3f7ebfe60bd9f4d80d81778b76da2530", null ],
    [ "VBS_PATH_NAME", "d3/d62/class_win32_service.html#ad29e608a9a56c3bf60844fbf2140c24a", null ],
    [ "VBS_STATE", "d3/d62/class_win32_service.html#a3ea4035124d5cd7a6ed81b828fb45c81", null ],
    [ "WIN32_ERROR_ACCESS_DENIED", "d3/d62/class_win32_service.html#a17dcfdbb9cbc43ec8df43dd82b3c8197", null ],
    [ "WIN32_ERROR_CIRCULAR_DEPENDENCY", "d3/d62/class_win32_service.html#a4f01defacfea5e4442267915fbcac1e2", null ],
    [ "WIN32_ERROR_DATABASE_DOES_NOT_EXIST", "d3/d62/class_win32_service.html#a4d966f1603b292ebc7ea526db2c78dc0", null ],
    [ "WIN32_ERROR_DEPENDENT_SERVICES_RUNNING", "d3/d62/class_win32_service.html#ad3cd70ef63814cbaf6e225103d764f8c", null ],
    [ "WIN32_ERROR_DUPLICATE_SERVICE_NAME", "d3/d62/class_win32_service.html#a28b23563acd48d53337ba636f44b3dce", null ],
    [ "WIN32_ERROR_FAILED_SERVICE_CONTROLLER_CONNECT", "d3/d62/class_win32_service.html#a24fd5a7de38bc84bd1fe758f30d37665", null ],
    [ "WIN32_ERROR_INSUFFICIENT_BUFFER", "d3/d62/class_win32_service.html#aa237282068432f054ccddf46c6afbb8c", null ],
    [ "WIN32_ERROR_INVALID_DATA", "d3/d62/class_win32_service.html#a357c5d5fee3e689caa1c8248f22ce481", null ],
    [ "WIN32_ERROR_INVALID_HANDLE", "d3/d62/class_win32_service.html#a28d32de1cda21d0ee433ee303a791604", null ],
    [ "WIN32_ERROR_INVALID_LEVEL", "d3/d62/class_win32_service.html#a1fcea82a878ecbb9b35f4438ccd7245c", null ],
    [ "WIN32_ERROR_INVALID_NAME", "d3/d62/class_win32_service.html#a2901e93d3ba1b28e74e3b5ee9809c904", null ],
    [ "WIN32_ERROR_INVALID_PARAMETER", "d3/d62/class_win32_service.html#a5fe98b6f7336c4e7a4f4fdb1fc2a80fa", null ],
    [ "WIN32_ERROR_INVALID_SERVICE_ACCOUNT", "d3/d62/class_win32_service.html#ad1af40365f6d7ee0bd9a8d4dc76bfc77", null ],
    [ "WIN32_ERROR_INVALID_SERVICE_CONTROL", "d3/d62/class_win32_service.html#a5d719b54a98276250e67db922d8a3f78", null ],
    [ "WIN32_ERROR_PATH_NOT_FOUND", "d3/d62/class_win32_service.html#a991958373dc6ff038bc3cfda70a048e6", null ],
    [ "WIN32_ERROR_SERVICE_ALREADY_RUNNING", "d3/d62/class_win32_service.html#a6e7a5b4a741beca3870a4b722a964222", null ],
    [ "WIN32_ERROR_SERVICE_CANNOT_ACCEPT_CTRL", "d3/d62/class_win32_service.html#a5dd76b4de775e2aba0b5534ab5320d9f", null ],
    [ "WIN32_ERROR_SERVICE_DATABASE_LOCKED", "d3/d62/class_win32_service.html#a12fdc75807f9f30ed35de19308a19021", null ],
    [ "WIN32_ERROR_SERVICE_DEPENDENCY_DELETED", "d3/d62/class_win32_service.html#a450cad522591884631d533fbd43bb501", null ],
    [ "WIN32_ERROR_SERVICE_DEPENDENCY_FAIL", "d3/d62/class_win32_service.html#a84dc2f60bab3488d6d7c466230655870", null ],
    [ "WIN32_ERROR_SERVICE_DISABLED", "d3/d62/class_win32_service.html#ab7f08a2e4139e5868db8a55a42d4674b", null ],
    [ "WIN32_ERROR_SERVICE_DOES_NOT_EXIST", "d3/d62/class_win32_service.html#ab1f931769f0835bc1413af5c53e0755c", null ],
    [ "WIN32_ERROR_SERVICE_EXISTS", "d3/d62/class_win32_service.html#aea30f3abf8de8d7f134f9f1936e1373e", null ],
    [ "WIN32_ERROR_SERVICE_LOGON_FAILED", "d3/d62/class_win32_service.html#aecde199b6e69896bb0272f89d774a1b1", null ],
    [ "WIN32_ERROR_SERVICE_MARKED_FOR_DELETE", "d3/d62/class_win32_service.html#a403c15ed74afc047950af5d7ece7c6f0", null ],
    [ "WIN32_ERROR_SERVICE_NO_THREAD", "d3/d62/class_win32_service.html#a8b365a3befc4ca9ca336acd9ff796c62", null ],
    [ "WIN32_ERROR_SERVICE_NOT_ACTIVE", "d3/d62/class_win32_service.html#a1a1a5c54ad50bafc77f43d3497ea5b01", null ],
    [ "WIN32_ERROR_SERVICE_REQUEST_TIMEOUT", "d3/d62/class_win32_service.html#a89109c254b03ed92ac904192ddd74d17", null ],
    [ "WIN32_ERROR_SHUTDOWN_IN_PROGRESS", "d3/d62/class_win32_service.html#abd64935965e6844b6985b587ca2eef7f", null ],
    [ "WIN32_NO_ERROR", "d3/d62/class_win32_service.html#a9cd1383ad7deb28bd288b3fb6f9ff165", null ],
    [ "WIN32_SERVICE_CONTINUE_PENDING", "d3/d62/class_win32_service.html#a72f6eab4028d46ee7cea158e6acb4f20", null ],
    [ "WIN32_SERVICE_NA", "d3/d62/class_win32_service.html#a3340bb22c687d819d611d0b823a70b7b", null ],
    [ "WIN32_SERVICE_PAUSE_PENDING", "d3/d62/class_win32_service.html#aec8909bd2294615a0787120371664d7f", null ],
    [ "WIN32_SERVICE_PAUSED", "d3/d62/class_win32_service.html#a100a89e2eb4f3917d7dcc59e97dd7ca2", null ],
    [ "WIN32_SERVICE_RUNNING", "d3/d62/class_win32_service.html#ab34d7bf8064bab9c742b10d9a4ef6d33", null ],
    [ "WIN32_SERVICE_START_PENDING", "d3/d62/class_win32_service.html#a12d6f025b1622f775bf8499bf60eee0d", null ],
    [ "WIN32_SERVICE_STOP_PENDING", "d3/d62/class_win32_service.html#aa4fa7932441ace0cd5bd168409f3da6a", null ],
    [ "WIN32_SERVICE_STOPPED", "d3/d62/class_win32_service.html#ae78d46fd637a116898019e78dd4c3254", null ]
];
