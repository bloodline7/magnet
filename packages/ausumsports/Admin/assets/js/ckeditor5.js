//import ClassicEditor from './ckeditor/ckeditor';
require ('./ckeditor/ckeditor');
import './ckeditor/styles.css';


class MyUploadAdapter {
    constructor( loader ) {
        // The file loader instance to use during the upload.
        this.loader = loader;
    }

    // Starts the upload process.
    upload() {
        return this.loader.file
            .then( file => new Promise( ( resolve, reject ) => {
                this._initRequest();
                this._initListeners( resolve, reject, file );
                this._sendRequest( file );
            } ) );
    }

    // Aborts the upload process.
    abort() {
        if ( this.xhr ) {
            this.xhr.abort();
        }
    }

    // Initializes the XMLHttpRequest object using the URL passed to the constructor.
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();
        xhr.open( 'POST', '/gceadmin/system/imageUpload', true );
        xhr.responseType = 'json';
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
    }

    // Initializes XMLHttpRequest listeners.
    _initListeners( resolve, reject, file ) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = `Couldn't upload file: ${ file.name }.`;

        xhr.addEventListener( 'error', () => reject( genericErrorText ) );
        xhr.addEventListener( 'abort', () => reject() );
        xhr.addEventListener( 'load', () => {
            const response = xhr.response;

            if ( !response || response.error ) {
                return reject( response && response.error ? response.error.message : genericErrorText );
            }

            // If the upload is successful, resolve the upload promise with an object containing
            // at least the "default" URL, pointing to the image on the server.
            // This URL will be used to display the image in the content. Learn more in the
            // UploadAdapter#upload documentation.
            resolve( {
                default: response.url
            } );
        } );

        // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
        // properties which are used e.g. to display the upload progress bar in the editor
        // user interface.
        if ( xhr.upload ) {
            xhr.upload.addEventListener( 'progress', evt => {
                if ( evt.lengthComputable ) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            } );
        }
    }

    // Prepares the data and sends the request.
    _sendRequest( file ) {
        // Prepare the form data.
        const data = new FormData();
        data.append( 'file', file );
        this.xhr.send( data );
    }
}

function CustomUploadAdapter( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        return new MyUploadAdapter( loader );
    };
}

const fontColorConfig = {
    columns: 8,
    documentColors: 24,
    colors: [
        {
            color: 'hsl(0, 0%, 0%)',
            label: 'Black'
        },
        {
            color: 'hsl(0, 0%, 30%)',
            label: 'Dim grey'
        },
        {
            color: 'hsl(0, 0%, 60%)',
            label: 'Grey'
        },
        {
            color: 'hsl(0, 0%, 90%)',
            label: 'Light grey'
        },
        {
            color: 'hsl(0, 0%, 100%)',
            label: 'White',
            hasBorder: true
        },
        {
            color: 'hsl(0, 75%, 60%)',
            label: 'Red'
        },
        {
            color: 'hsl(30, 75%, 60%)',
            label: 'Orange'
        },
        {
            color: 'hsl(60, 75%, 60%)',
            label: 'Yellow'
        },
        {
            color: 'hsl(90, 75%, 60%)',
            label: 'Light green'
        },
        {
            color: 'hsl(120, 75%, 60%)',
            label: 'Green'
        },
        {
            color: 'hsl(150, 75%, 60%)',
            label: 'Aquamarine'
        },
        {
            color: 'hsl(180, 75%, 60%)',
            label: 'Turquoise'
        },
        {
            color: 'hsl(210, 75%, 60%)',
            label: 'Light blue'
        },
        {
            color: 'hsl(240, 75%, 60%)',
            label: 'Blue'
        },
        {
            color: 'hsl(270, 75%, 60%)',
            label: 'Purple'
        }
    ]
};



$(document).on('load', function (event, target) {

    if($(target.target).find("#ckeditor").length)
    {
        const watchdog = new CKSource.EditorWatchdog();
        window.watchdog = watchdog;
        watchdog.setCreator( ( element, config ) => {
            return CKSource.Editor
                .create( element, config )
                .then( editor => {

                    $("#ckeditor").parents("form:first").get(0).beforeSubmit = function ()
                    {
                        console.log('Data Setting ------------------');
                        $("#ckeditor").val( editor.getData());
                    };

                    return editor;
                } )
        } );

        watchdog.setDestructor( editor => {
            return editor.destroy();
        } );

        watchdog.on( 'error', handleError );

        watchdog
            .create( document.querySelector( '#ckeditor' ), {

                licenseKey: '',
                extraPlugins: [CustomUploadAdapter]

            }).then( newEditor => {
             /* window.ckeditor = newEditor;
              console.log(newEditor);
              console.log('---------------dddddddd---------------');
              console.log(window.ckeditor);*/
            })
            .catch( handleError );

        function handleError( error ) {
            console.error( 'Oops, something went wrong!' );
            console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
            console.warn( 'Build id: vel1o379wdr-xa4yzaxh03vq' );
            console.error( error );
        }


        /*Editor.create( document.querySelector( '#ckeditor' ), {
            extraPlugins: [CustomUploadAdapter]
        })
            .then( editor => {
                window.editor = editor;
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            });*/
    }
    else  {

        return false;
    }

});
