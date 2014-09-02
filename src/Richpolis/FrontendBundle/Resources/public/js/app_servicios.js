window.Servicios = {};

Servicios.Views = {};
Servicios.Collections = {};
Servicios.Models = {};
Servicios.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Servicios.Models.Servicio = Backbone.Model.extend({
    defaults: {
      servicio: '',
      descripcion: '',
      slug: '',
      galerias: [],
    }
});

Servicios.Collections.Servicios = Backbone.Collection.extend({
    model: Servicios.Models.Servicio,
});

//esta vista es para visualizar el show_template
Servicios.Views.Show = Backbone.View.extend({
    el: 'div.servicios',
    tagName: 'div',
    template: swig.compile($("#show_template").html()),
    initialize: function() {
        //this.model.on("change", this.render, this);
    },
    render: function() {
        var data = this.model.toJSON();
		var self = this;
		this.$el.fadeOut("fast",function(){
            self.$el.empty();
			self.$el.html(self.template(data));
			self.$el.fadeIn("fast",function(){
				self.iniciarComponentes();
			});
		});
        
        return this;
    },
    iniciarComponentes: function(){
        iniciarSlider();
        iniciarFancybox();
    }
});


//vista collecio que recibe todos los modelos.
Servicios.Views.ListView = Backbone.View.extend({
    el: 'article.servicios',
    tagName: 'article',
    template: swig.compile($("#app_template").html()),
    render: function() {
        var data = this.collection.toJSON();
        var html = this.template({servicios: data});
        this.$el.html(html);
        return this;
    },
});

Servicios.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root",
        "servicio/:slug" : "show"
    },
    root: function() {
        debugger;
        if(!window.views.listview){
            this.renderListView();
        }
        this.show(window.collections.servicios.models[0].attributes.slug);
    },
    renderListView: function(){
        window.views.listview = new Servicios.Views.ListView({
            collection: window.collections.servicios,
        });
        views.listview.render();
    },
    show: function(slug) {
        debugger;
        if(!window.views.listview){
            this.renderListView();    
        }
        var models = window.collections.servicios.where({slug: slug});
        if(!models[0]){
            window.routers.app.navigate("/",true);
        }

        $("li.item").removeClass('active');
        $("#item-"+slug).addClass('active');

        this.mostrarServicio(models[0]);
    },
    mostrarServicio: function(model){
        debugger;
        $(".loader").fadeIn();
        if(!window.views.show){
            window.views.show = new  Servicios.Views.Show({model: model});
            views.show.render();
        }else{
            window.views.show.model= model;
            views.show.render({silence: true});
        }
        views.show.iniciarComponentes();
        $(".loader").fadeOut();
    },
});