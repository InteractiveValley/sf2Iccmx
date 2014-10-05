window.Proyectos = {};

Proyectos.Views = {};
Proyectos.Collections = {};
Proyectos.Models = {};
Proyectos.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Proyectos.Models.Proyecto = Backbone.Model.extend({
    defaults: {
      titulo: '',
      descripcion: '',
      slug: '',
      galerias: [],
      seleccionado: false,
      visible: true, 
    }
});

Proyectos.Collections.Proyectos = Backbone.Collection.extend({
    model: Proyectos.Models.Proyecto,
    filtrarResultados: function(){
        var self = this;
        this.empezo = $("#rangoProyectos").val();
        this.categoria = $("#categoriaProyectos").val();
        this.each(function (proyecto) {
            var categoria = ( proyecto.get('categoria')==self.categoria || self.categoria == 'todos' );
            var rango = (proyecto.get('empezo') >= self.empezo);
            if( rango && categoria){
                proyecto.set({visible: true});
            }else{
                proyecto.set({visible: false});
            }
		});
    }
});



//esta vista es para visualizar el show_template
Proyectos.Views.ItemView = Backbone.View.extend({
    tagName: 'li',
    className: 'proyecto',
    template: swig.compile($("#item_template").html()),
    events: {
        'click figure.imagen':'seleccionarProyecto', 
        'click .og-close': 'cerrarProyecto'
    },
    cerrarProyecto: function(){
        this.model.set({seleccionado: false});
    },
    seleccionarProyecto: function(){
        this.model.set({seleccionado: true});
    },
    initialize: function() {
        this.model.on("change:seleccionado", this.mostrarExpander, this);
        this.model.on("change:visible",this.mostrar,this);
    },
    render: function() {
        var data = this.model.toJSON();
        var html = this.template(data);
        this.$el.html(html);
        return this;
    },
    mostrarExpander: function() {
        var self = this;
        if(this.model.get('seleccionado')){
            var data = this.model.toJSON();
            var template = swig.compile($("#item_seleccionado_template").html())
            var html = template(data);
            this.$el.append(html);
            this.$el.addClass('og-expanded');    
            this.iniciarComponentes();    
        }else{
            this.$el.find(".og-expander").fadeOut("fast",function(){
                self.$el.find(".og-expander").remove();
            });
            this.$el.removeClass('og-expanded');
        }
        return this;
    },
    mostrar: function(){
      if(this.model.get("visible")){
          this.$el.fadeIn("fast");
      }else{
          this.$el.fadeOut("fast");
      }  
    },
    iniciarComponentes: function(){
        iniciarSlider();
        iniciarFancybox();
    }
});


//vista collecio que recibe todos los modelos.
Proyectos.Views.ListView = Backbone.View.extend({
    el: 'article.proyectos',
    tagName: 'article',
    //template: swig.compile($("#app_template").html()),
    events: {
        "change #categoriaProyectos": "seleccionarProyectos",
        "change #rangoProyectos": "seleccionarProyectos"
    },
    seleccionarProyectos: function(event){
        event.preventDefault();
        this.collection.filtrarResultados();
    },
    AddOne: function(proyecto) {
        var indice = 0;
        
        if(window.views.proyectos && window.views.proyectos.length){
            indice = window.views.proyectos.length;
        }else{
            indice = 0;
            window.views.proyectos = [];
        }

        //if( rango && categoria) {
            window.views.proyectos[indice]= new Proyectos.Views.ItemView({model: proyecto});
            var html = window.views.proyectos[indice].render().el;
            this.$el.find(".contenedorProyectos").append(html); 
        //}    
    },
    render: function() {
        this.collection.empezo = $("#rangoProyectos").val();
        this.collection.categoria = $("#categoriaProyectos").val();
        this.renderAll();
        this.iniciarGrid();
        return this;
    },
    renderAll: function(){
        this.borrarViewsItems();
        this.collection.forEach(this.AddOne,this);
        return this;
    },
    borrarViewsItems: function() {
        var indice = 0;
        if(window.views.proyectos && window.views.proyectos.length){
            indice = window.views.proyectos.length;
        }else{
            indice = 0;
            window.views.proyectos = [];
        }
        for(var cont = 0; cont < indice; cont++ ){
            window.views.proyectos[cont].remove();
        }
    },
    iniciarGrid: function(){
      Grid.init();
    },
    
});

Proyectos.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root"
    },
    root: function() {
        debugger;
        window.views.listview = new Proyectos.Views.ListView({
            collection: window.collections.proyectos,
        });
        views.listview.render();
    },
});
