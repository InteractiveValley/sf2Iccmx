window.Categorias = {};

Categorias.Views = {};
Categorias.Collections = {};
Categorias.Models = {};
Categorias.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Categorias.Models.Categoria = Backbone.Model.extend({
    defaults: {
      nombre: '',
      is_active: true,
      position: 0,
    }
});

Categorias.Collections.Categorias = Backbone.Collection.extend({
    model: Categorias.Models.Categoria,
    comparator: 'position',
});

//esta vista recibe un modelo CategoriaModel para que pueda renderear su contenido
Categorias.Views.Item = Backbone.View.extend({

    tagName: "tr",
    
    template: swig.compile($("#item_table_template").html()),
    
    className: 'registro-sorteable',
    
    events: {
      "click .acciones .boton-up" : "upCategoria",
      "click .acciones .boton-down" : "downCategoria",  
    },
    initialize: function() {
        this.model.on("change", this.render, this);
        this.id = "registro-" + this.model.get("id");
    },
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data)).attr({id: 'registro-'+this.model.get('id')});
        return this;
    },
    upCategoria: function(e){
      var self = this;
      e.preventDefault();
      e.stopPropagation();
      console.log("upCategoria");
      $(".loader").fadeIn();
      if(!window.api.accion.up){
            window.api.accion.up = true;
            $.ajax({
               type: 'PATCH', 
               url: window.api.url + "/" +self.model.get('id')+"/up",
               dataType: 'json',
            success: function(data){
                console.log(data);
                self.actualizarRoot();
            },
            error: function(data){
                console.log(data);
                self.actualizarRoot();
            },
        });
      }
    },
    downCategoria: function(e){
      var self = this;
      e.preventDefault();
      e.stopPropagation();
      console.log("downCategoria");
      $(".loader").fadeIn();
      if(!window.api.accion.down){
        window.api.accion.down = true;
        $.ajax({
            type: 'PATCH', 
            url: window.api.url + "/" +self.model.get('id')+"/down",
            dataType: 'json',
            success: function(data){
                debugger;
                console.log(data);
                self.actualizarRoot();
            },
            error: function(data){
                debugger;
                console.log(data);
                self.actualizarRoot();
            },
        });
      }
    },
    actualizarRoot: function(){
        $("#cuerpoTabla").empty();
        window.collections.categorias.reset();
        var xhr = window.collections.categorias.fetch();
        xhr.done(function(){
           //window.routers.app.navigate('/',true);
           window.api.accion.up = false;
           window.api.accion.down = false;
           $(".loader").fadeOut();
        });
        $(".loader").fadeOut();
    },

});

//esta vista es para visualizar el show_template
Categorias.Views.Show = Backbone.View.extend({

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
                    window.collections.categorias.reset();
                    var xhr = window.collections.categorias.fetch();
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
Categorias.Views.FormNew = Backbone.View.extend({

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
                var data = $("#formCategorias").serialize();
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
                    var model = new Categorias.Models.Categoria(data);
                    window.collections.categorias.add(model);
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
        var $nombre = $("input#nombre");
        
        // validar el nombre 
        if($nombre.val().length == 0){
            this.errorNoty("El nombre esta vacio.");
            $nombre.focus();
            return false;
        }else if($nombre.val().length < 3){
            this.errorNoty("El nombre es demasiado corto, minimo 3 caracteres");
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

//esta vista es para visualizar el formulario
Categorias.Views.FormEdit = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    events: {
        'click #botonRegresar': 'regresar',
        'click #botonGuardar' : 'guardar',
        'click #botonEliminar' : 'eliminar',
    },
    
    initialize: function(data) {
        this.contenido = data;
        window.api.modelo.id = this.model.get('id');
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
                var data = $("#formCategorias").serialize();
                $.ajax({
                    url: window.api.url + "/" +window.api.modelo.id,
                    type: 'PUT',
                    data: data,
                    dataType: 'json',
                }).done(function(data, textStatus, jqXHR){
                    window.api.accion.guardar = false;
                    window.collections.categorias.reset();
                    var xhr = window.collections.categorias.fetch();
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
                    window.collections.categorias.reset();
                    var xhr = window.collections.categorias.fetch();
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
            this.errorNoty("La categoria esta vacia.");
            $nombre.focus();
            return false;
        }else if($nombre.val().length < 3){
            this.errorNoty("La categoria es demasiada corto, minimo 3 caracteres");
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
Categorias.Views.Table = Backbone.View.extend({

    el: '#contenidoPagina',
    
    template: swig.compile($("#table_template").html()),
    
    viewItems: [],
    
    initialize: function() {

        this.cuerpoTabla = "#cuerpoTabla";
        
        this.collection.on("add", this.AddOne,this);
        
    },
    
    AddOne: function(categoria) {
        var categoriaView = new Categorias.Views.Item({model: categoria});
        $(this.cuerpoTabla).append(categoriaView.render().el);
        return categoriaView;
    },
    
    render: function() {
        var that= this;
        this.$el.html(this.template());
        this.collection.each(function(item){
            that.viewItems.push(that.AddOne(item));
            return this;
        });
        this.inicializarDatatable();
        iniciarlizarSorteable();
        return this;
    },
    
    inicializarDatatable: function() {
        /*$('#datatable').dataTable({
            "sPaginationType": "full_numbers",
            "sNext": "Siguiente",
            "sLast": "Ultimo",
            "sFirst": "Primero",
            "sPrevious": "Anterior",
            "aaSorting": [[ 2, "asc" ]]
        });*/
    },
    listUpdate: function(){
        _.each(this.viewItems, function(item){
            item.model.set('position', item.$el.index()+1);
        });
        this.collection.sort({silent: true})
          _.invoke(this.viewItems, 'remove');
        this.render();
    }
});

Categorias.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root",
        "new" : "new",
        "edit/:id" : "edit",
        "show/:id" : "show"
    },
    
    root: function() {
        window.views.table = new Categorias.Views.Table({
            collection: window.collections.categorias,
        });
        views.table.render();
    },
    
    new: function() {
        $(".loader").fadeIn();
        $.ajax({
           type: 'GET',
           url: window.api.url + "/new",
           dataType: 'html',
        }).done(function(data){
            debugger;
            if(window.views.formEdit)window.views.formEdit.remove();
            if(window.views.show)window.views.show.remove();
            window.views.formNuevo = new  Categorias.Views.FormNew({data: data});
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
            var model = window.collections.categorias.get(id);
            window.views.formEdit = new  Categorias.Views.FormEdit({data: data, model: model});
            $("#contenidoPagina").html(views.formEdit.render().el).fadeIn("fast");
            $(".loader").fadeOut();
        }).fail(function(){
            $(".loader").fadeOut();
            alert("Error al cargar la pagina de editar registro");
        });
    },
    show: function(id) {
        debugger;
        if(window.views.formNuevo)window.views.formNuevo.remove();
        if(window.views.formEdit)window.views.formEdit.remove();
        var model = window.collections.categorias.get(id);
        window.views.show = new  Categorias.Views.Show({model: model});
        $("#contenidoPagina").html(views.show.render().el).fadeIn("fast");
    },
    
});
