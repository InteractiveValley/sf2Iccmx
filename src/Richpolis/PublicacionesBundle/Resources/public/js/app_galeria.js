window.Galerias = {};

Galerias.Views = {};
Galerias.Collections = {};
Galerias.Models = {};
Galerias.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Galerias.Models.Galeria = Backbone.Model.extend({
    defaults: {
      nombre: '',
      is_active: true,
      position: 0,
    }
});

Galerias.Collections.Galerias = Backbone.Collection.extend({
    model: Galerias.Models.Galeria,
});

//esta vista recibe un modelo GaleriaModel para que pueda renderear su contenido
Galerias.Views.Item = Backbone.View.extend({

    tagName: "tr",
    
    template: swig.compile($("#item_table_template").html()),
    events: {
      "click .acciones .boton-up" : "upGaleria",
      "click .acciones .boton-down" : "downGaleria",  
    },
    initialize: function() {
        this.model.on("change", this.render, this);
        this.id = "registro-" + this.model.get("id");
    },
    render: function() {
        var data = this.model.toJSON();
        console.log(data);
        this.$el.html(this.template(data)).attr({id: 'registro-'+this.model.get('id')});
        return this;
    },
});

//esta vista es para visualizar el show_template
Galerias.Views.Show = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    template: swig.compile($("#show_template").html()),

    events: {
        'click #botonRegresar': 'regresar',
        'click #botonEliminar' : 'eliminar',
    },
    initialize: function() {
        this.model.on("change", this.render, this);
        window.api.modelo.id = this.model.get('id');
    },
    regresar : function(e){
        e.preventDefault();
        e.stopPropagation();
        console.log("regresar");
        window.routers.app.navigate('/',true);
    },
    eliminar : function(e){
        $(".loader").fadeIn();
        e.preventDefault();
        e.stopPropagation();
        if(!window.api.accion.eliminar && window.api.modelo.id == this.model.get('id')){
            window.api.accion.eliminar = true;
            console.log("Accion eliminar");
            this.model.urlRoot = window.api.url;
            this.model.destroy({
                success: function(){
                    window.collections.galerias.reset();
                    var xhr = window.collections.galerias.fetch();
                    xhr.done(function(){
                        window.routers.app.navigate('/',true);
                        $(".loader").fadeOut();
                        window.api.accion.eliminar = false;
                    });
                }
            });
        }
    },
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data));
        return this;
    }

});

//esta vista es para visualizar el formulario
Galerias.Views.FormNewImagen = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    events: {
        'click #botonRegresar': 'regresar',
        'click #botonGuardar' : 'guardar',
    },
    initialize: function(data) {
        this.contenido = data;
        window.api.modelo.id = 0;
    },
    regresar : function(e){
        e.stopPropagation();
        console.log("regresar");
        window.routers.app.navigate('/',true);
    },
    guardar: function(e){
        debugger;
        e.preventDefault();
        e.stopPropagation();
        var self = this;
        if(!window.api.accion.guardar  && window.api.modelo.id == 0){
            window.api.accion.guardar=true;
            if(this.validarForm()){
                $(".loader").fadeIn();
                this.uploadFile();
            }else{
                $(".loader").fadeOut();
                window.api.accion.guardar = false;
            }
        }
    },
    render: function() {
        this.$el.html(this.contenido.data);
        return this;
    },
    validarForm: function(){
        var $file = $("input#file");
        
        // validar el nombre 
        if($file.val().length == 0){
            this.errorNoty("No hay archivo seleccionado.");
            $nombre.focus();
            return false;
        }

        return true;
    },
    errorNoty: function(mensaje){
        noty({text: mensaje, layout:'topRight', type:'error',timeout:2000});
    },
    parseErrores: function(data){
        debugger;
        var json = $.parseJSON(data.responseText);
        var children = json.errors.children;
        var stringError ="";
        var cont=0;
        var contb=0;
        for(cont = 0; cont< children.length;  cont++){
            if(children[cont]['errors']){
                for(contb=0; contb<children[cont]['errors'];contb++){
                    stringError +=children[cont]['errors'][contb];
                }
            }
        }
        this.errorNoty(stringError);
    },
    uploadFile: function() {
        var self = this;
        this.model = new Galerias.Models.Galeria();
        this.model.urlRoot = window.api.url;
        this.model.save($("#formGalerias").serialize(), { 
            iframe: true,
            files: $('#formGalerias :file'),
            data: values,
            success: function(data){
                debugger;
                window.api.accion.guardar = false;
                console.log("Creado")
                console.log(data)
                var model = new Galerias.Models.Galeria(data);
                window.collections.galerias.add(model);
                window.routers.app.navigate("show/"+model.get('id'),true);
                $(".loader").fadeOut();
            },
            error: function(data){
                debugger;
                window.api.accion.guardar = false;
                $(".loader").fadeOut();
                console.log(data);
                self.parseErrores(data);
            }
        });
  }

});

//esta vista es para visualizar el formulario
Galerias.Views.FormNewLinkVideo = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    events: {
        'click #botonRegresar': 'regresar',
        'click #botonGuardar' : 'guardar',
    },
    initialize: function(data) {
        this.contenido = data;
        window.api.modelo.id = 0;
    },
    regresar : function(e){
        e.stopPropagation();
        console.log("regresar");
        window.routers.app.navigate('/',true);
    },
    guardar: function(e){
        debugger;
        e.preventDefault();
        e.stopPropagation();
        var self = this;
        if(!window.api.accion.guardar  && window.api.modelo.id == 0){
            window.api.accion.guardar=true;
            if(this.validarForm()){
                $(".loader").fadeIn();
                var data = $("#formGalerias").serialize();
                $.ajax({
                    url: window.api.url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                }).done(function(data, textStatus,jqXHR){
                    debugger;
                    window.api.accion.guardar = false;
                    console.log("Creado")
                    console.log(data)
                    var model = new Galerias.Models.Galeria(data);
                    window.collections.galerias.add(model);
                    window.routers.app.navigate("show/"+model.get('id'),true);
                    $(".loader").fadeOut();
                }).fail(function(data, textStatus,jqXHR){
                    debugger;
                    window.api.accion.guardar = false;
                    $(".loader").fadeOut();
                    console.log(data);
                    self.parseErrores(data);
                });
            }else{
                $(".loader").fadeOut();
                window.api.accion.guardar = false;
            }
        }
    },
    render: function() {
        this.$el.html(this.contenido.data);
        return this;
    },
    validarForm: function(){
        var $file = $("input#archivo");
        
        // validar el nombre 
        if($file.val().length == 0){
            this.errorNoty("Debe agregar un link para crear link de video.");
            $file.focus();
            return false;
        }

        return true;
    },
    errorNoty: function(mensaje){
        noty({text: mensaje, layout:'topRight', type:'error',timeout:2000});
    },
    parseErrores: function(data){
        debugger;
        var json = $.parseJSON(data.responseText);
        var children = json.errors.children;
        var stringError ="";
        var cont=0;
        var contb=0;
        for(cont = 0; cont< children.length;  cont++){
            if(children[cont]['errors']){
                for(contb=0; contb<children[cont]['errors'];contb++){
                    stringError +=children[cont]['errors'][contb];
                }
            }
        }
        this.errorNoty(stringError);
    }

});

//esta vista es para visualizar el formulario
Galerias.Views.FormEdit = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    events: {
        'click #botonRegresar': 'regresar',
        'click #botonGuardar' : 'guardar',
        'click #botonEliminar' : 'eliminar',
    },
    
    initialize: function(data) {
        var publicacion = this.model.get('publicacion');
        this.contenido = data;
        window.api.modelo.id = this.model.get('id');
        window.api.publicacion = publicacion.id;
    },
    
    regresar : function(e){
        e.stopPropagation();
        console.log("regresar");
        window.routers.app.navigate('/',true);
    },
    
    guardar: function(e){
        debugger;
        e.preventDefault();
        e.stopPropagation();
        if(!window.api.accion.guardar && window.api.modelo.id == this.model.get('id')){
            window.api.accion.guardar = true;
            var self = this;
            if(this.validarForm()){
                $(".loader").fadeIn();
                this.model.set({'nombre': $("input#nombre").val()});
                var data = $("#formGalerias").serialize();
                $.ajax({
                    url: window.api.url + "/" +window.api.modelo.id,
                    type: 'PUT',
                    data: data,
                    dataType: 'json',
                }).done(function(data, textStatus, jqXHR){
                    window.api.accion.guardar = false;
                    window.collections.galerias.reset();
                    var xhr = window.collections.galerias.fetch();
                    xhr.done(function(){
                        window.routers.app.navigate('show/'+window.api.modelo.id,true);
                        $(".loader").fadeOut();
                    });
                }).fail(function(data, textStatus, errorThrown){
                   debugger;
                   window.api.accion.guardar = false;
                   $(".loader").fadeOut();
                   console.log($.parseJSON(data.responseText));
                   self.parseErrores(data);
                });
            }else{
                window.api.accion.guardar = false;
                $(".loader").fadeOut();
            }
        }
    },
    
    eliminar : function(e){
        var self = this;
        $(".loader").fadeIn();
        e.preventDefault();
        e.stopPropagation();
        if(!window.api.accion.eliminar  && window.api.modelo.id == this.model.get('id')){
            window.api.accion.eliminar = true;
            console.log("Accion eliminar");
            this.model.urlRoot = window.api.url;
            this.model.destroy({
                success: function(){
                    window.collections.galerias.reset();
                    var xhr = window.collections.galerias.fetch();
                    xhr.done(function(){
                        window.routers.app.navigate('/',true);
                        window.api.accion.eliminar = false;
                        $(".loader").fadeOut();
                    });
                }
            });
        }
    },
    
    render: function() {
        this.$el.html(this.contenido.data);
        return this;
    },

    validarForm: function(){
        var $nombre = $("input#nombre");

        // validar el nombre 
        if($nombre.val().length == 0){
            this.errorNoty("La galeria esta vacia.");
            $nombre.focus();
            return false;
        }else if($nombre.val().length < 3){
            this.errorNoty("La galeria es demasiada corto, minimo 3 caracteres");
            $nombre.focus();
            return false;
        }
        
        return true;
    },

    errorNoty: function(mensaje){
         noty({text: mensaje, layout:'topRight', type:'error',timeout:2000});
    },
    
    parseErrores: function(data){
        debugger;
        var json = $.parseJSON(data.responseText);
        var children = json.errors.children;
        var stringError ="";
        var cont=0;
        var contb=0;
        for(cont = 0; cont< children.length;  cont++){
            if(children[cont]['errors']){
                for(contb=0; contb<children[cont]['errors'];contb++){
                    stringError +=children[cont]['errors'][contb];
                }
            }
        }
        this.errorNoty(stringError);
    }

});


//vista collecio que recibe todos los modelos.
Galerias.Views.Table = Backbone.View.extend({

    el: '#contenidoPagina',
    
    template: swig.compile($("#table_template").html()),
    
    initialize: function() {

        this.cuerpoTabla = "#cuerpoTabla";
        
        this.collection.on("add", this.AddOne,this);
        //esta es para que ejecute el render inicial
        //this.render();
        
    },
    
    AddOne: function(galeria) {
        var galeriaView = new Galerias.Views.Item({model: galeria});
        var html = galeriaView.render().el;
        $(this.cuerpoTabla).append(html);
    },
    
    render: function() {
        this.$el.html(this.template());
        this.collection.forEach(this.AddOne,this);
        this.inicializarDatatable();
        return this;
    },
    
    inicializarDatatable: function() {
        $('#datatable').dataTable({
            "sPaginationType": "full_numbers",
            "sNext": "Siguiente",
            "sLast": "Ultimo",
            "sFirst": "Primero",
            "sPrevious": "Anterior",
            "aaSorting": [[ 4, "asc" ]]
        });
    },
});

Galerias.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root",
        "newImagen" : "newImagen",
        "newLinkVideo" : "newLinkVideo",
        "edit/:id" : "edit",
        "show/:id" : "show"
    },
    
    root: function() {
        window.views.table = new Galerias.Views.Table({
                collection: window.collections.galerias,
        });
        views.table.render();
    },
    
    newImagen: function() {
        $(".loader").fadeIn();
        $.ajax({
           type: 'GET',
           url: window.api.url + "/new",
           dataType: 'html',
           data: {tipo: window.api.tipos['Imagen']}
        }).done(function(data){
            debugger;
            if(window.views.formEdit)window.views.formEdit.remove();
            if(window.views.show)window.views.show.remove();
            window.views.formNuevo = new  Galerias.Views.FormNewImagen({data: data});
            $("#contenidoPagina").html(views.formNuevo.render().el).fadeIn("fast");
            $(".loader").fadeOut();
        }).fail(function(){
            $(".loader").fadeOut();
            alert("Error al cargar la pagina de nuevo registro");
        });
    },
    newLinkVideo: function() {
        $(".loader").fadeIn();
        $.ajax({
           type: 'GET',
           url: window.api.url + "/new",
           dataType: 'html',
           data: {tipo: window.api.tipos['Link video']}
        }).done(function(data){
            debugger;
            if(window.views.formEdit)window.views.formEdit.remove();
            if(window.views.show)window.views.show.remove();
            window.views.formNuevo = new  Galerias.Views.FormNewLinkVideo({data: data});
            $("#contenidoPagina").html(views.formNuevo.render().el).fadeIn("fast");
            $(".loader").fadeOut();
        }).fail(function(){
            $(".loader").fadeOut();
            alert("Error al cargar la pagina de nuevo registro");
        });
    },
    edit: function(id) {
        $(".loader").fadeIn();
        $.ajax({
           type: 'GET', 
           url: window.api.url + "/" +id+"/edit",
           dataType: 'html',
        }).done(function(data){
            debugger;
            if(window.views.formNuevo)window.views.formNuevo.remove();
            if(window.views.show)window.views.show.remove();
            var model = window.collections.galerias.get(id);
            window.views.formEdit = new  Galerias.Views.FormEdit({data: data, model: model});
            $("#contenidoPagina").html(views.formEdit.render().el).fadeIn("fast");
            $(".loader").fadeOut();
            $("#publicacion").val(window.api.publicacion);
        }).fail(function(){
            $(".loader").fadeOut();
            alert("Error al cargar la pagina de editar registro");
        });
    },
    show: function(id) {
        debugger;
        if(window.views.formNuevo)window.views.formNuevo.remove();
        if(window.views.formEdit)window.views.formEdit.remove();
        var model = window.collections.galerias.get(id);
        window.views.show = new  Galerias.Views.Show({model: model});
        $("#contenidoPagina").html(views.show.render().el).fadeIn("fast");
    },
});
