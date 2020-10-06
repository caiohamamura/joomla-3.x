<?php
/**
 * @package     
 * @subpackage  
 * @copyright   
 * @license     
 */

defined('_JEXEC') or die;
?>
<style>
	.pagenav {
		transition: all 0.2s ease-in-out;
	}
	.pagina-transition-enter-active {
		transition: all 0.2s;
	}
	.pagina-transition-enter{
		display: inline-block;
		opacity:0;
	}
	.pagina-transition-enter-to {
		display: inline-block;
		opacity:1;
	}
	.pagina-transition-leave-active{
		transition: all 0.3s ease-in-out;
		opacity: 1;
	}
	.pagina-transition-leave-to {
		opacity:0;
	}
	.listagem-chamadas-secundarias {
		transition: height 0.5s ease-in-out;
		overflow: hidden;
	}
	.list-enter-active {
		display:none;
		transition-delay: 0.3s;
		transition: all 0.5s ease-in-out;
	}
	.list-enter {
		display:inline-block;
		opacity:0.4;
		transform:scaleY(0);
	}
	.list-enter-to {
		display:inline-block;
		opacity:1;
		transform:scaleY(1);
	}
	.list-leave-active {
		transition: all 0.2s ease-in-out;
	}
	.list-leave-to {
		opacity: 0.4;
		float: right;
		transform: scaleY(0);
	}
	.nolink {
		color: black;
		cursor: default;
	}
	.nolink:hover {
		color: initial !important;
		background-color: initial !important;
	}
	.nolink:active {
	    background-color: #f5f5f5 !important;
	}
	.nolink:focus {
		background-color: initial !important;
		color: initial;
	}
</style>
<div id="chamadas-secundarias">
<div class="listagem-chamadas-secundarias">
	<transition-group name="list"  mode="out-in" tag="p" v-on:after-leave="onEnter">
		<div class="row-fluid" v-for="item in items" :key="item.id">
			<div class="list-article-container">
				<?php if ($params->get('exibir_imagem')) : ?>
				<div class="image-container" v-if="item.image_url">
					<a href="{{item.chapeu}}" target="_blank">
						<img src="{{item.image_url}}" width="200" height="130" class="img-rounded" alt="{{item.image_alt}}" />
					</a>
				</div>
				<?php endif; ?>		
				<div class="content-container">
					<h3>
						<a :href="getLink(item)" target="_blank">
							{{getTitle(item)}}
						</a>
					</h3>
					<div class="description" v-if="params.exibir_introtext != '0'" v-html="item.introtext">
					</div>
				</div>
			</div>
		</div>
	</transition-group>
</div>
<div class="pagination">
		<p class="counter pull-left">
			Página <transition name="pagina-transition" mode="out-in"><span :key="this.page">{{this.page}}</span></transition> de {{this.page_total}} 
		</p>
	</transition>
	<ul class="pull-right">
        <template>
		<li class="pagination-start"><a title="Início" href="#" v-on:click="gotoPagina($event,1)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == 1 }"><<</a></li>
		<li class="pagination-prev"><a title="Ant" href="#" v-on:click="gotoPagina($event,page-1)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == 1 }"><</a></li>
        </template>
		<template v-for="pagina in page_range">
			<li><a class="pagenav" v-bind:class="{ nolink: pagina == page }" href="#" v-on:click="gotoPagina($event,pagina)">{{pagina}}</a></li>
        </template>
		<template>
			<li class="pagination-next"><a title="Próximo" href="#" v-on:click="gotoPagina($event,page+1)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == this.page_total }">></a></li>
			<li class="pagination-end"><a title="Fim" href="#" v-on:click="gotoPagina($event,page_total)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == this.page_total }">>></a></li>
        </template>
    </ul>
</div>
<?php if (! empty($link_saiba_mais) ): ?>
	<div class="outstanding-footer">
		<a href="<?php echo $link_saiba_mais; ?>" class="outstanding-link">
			<?php if ($params->get('texto_saiba_mais')): ?>
				<span class="text"><?php echo $params->get('texto_saiba_mais')?></span>
			<?php else: ?>
				<span class="text">saiba mais</span>
			<?php endif;?>
			<span class="icon-box">                                          
		      <i class="icon-angle-right icon-light"><span class="hide">&nbsp;</span></i>
		    </span>
		</a>	
	</div>
<?php endif; ?>
</div>
<script>
var chSecund=new Vue({
    el: '#chamadas-secundarias',
    data: {
		items: <?php echo json_encode($lista_chamadas); ?>,
		page: 0,
		max_pages: 5,
		page_range: [1,2,3],
		page_total: 10,
		cache_items: {},
		timeoutId: 0,
		params: <?php echo json_encode($params); ?>
    },
    beforeCreate: function() {
        if ("chSecund" in Object.assign({},history.state)) {
            document.querySelector("#chamadas-secundarias").innerHTML = history.state.chSecund;
        } else { 
            var chamadasSecundarias = {"chSecund": document.querySelector("#chamadas-secundarias").innerHTML};

            history.pushState(Object.assign({}, history.state, chamadasSecundarias),document.title,document.location.href);
        }
    },
	created: function() {
		var vm = this;
		var catids = JSON.stringify(vm.params.catid);
		this.checkSize();
		window.addEventListener('resize', this.checkSize.bind(this));
		fetch(`index.php?api&command=count&catid=${catids}`).then(function(res) {
			res.json().then(function(data) {
				vm.page_total = Math.ceil(data[0].contagem / vm.params.quantidade);
				vm.updatePageRange();
			}.bind(this));
		}.bind(this));
		this.gotoPagina(null,1,this.onEnter);
	},
    methods: {
		onEnter: function(e){
			clearTimeout(this.timeoutId);
			this.timeoutId = setTimeout(function() {
				jQuery(".listagem-chamadas-secundarias")[0].style.height=jQuery(".listagem-chamadas-secundarias>p")[0].scrollHeight + "px";
			}, 100);
		},
		checkSize: function() {
			var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
			if (width < 450) this.max_pages = 3
			else if (width < 750) this.max_pages = 5
			else this.max_pages = 10
			this.updatePageRange();
		},
		updatePageRange: function() {
			var pageMin = this.page - (Math.floor(this.max_pages / 2));
			pageMin = pageMin <= 0 ? 1 : pageMin;
			var pageMax = pageMin + (this.max_pages - 1);
			pageMax = pageMax > this.page_total ? this.page_total : pageMax;
			if (pageMax - pageMin < this.max_pages) {
				pageMin = pageMax - (this.max_pages - 1);
				pageMin = pageMin <= 0 ? 1 : pageMin;
			}
			var size = pageMax - pageMin + 1;
			this.page_range = Array.apply(0, {length: size}).map(function(e,i){return i+pageMin});
		},
        getTitle: function(item) {
            var theTitle = item.title;
            var legend = JSON.parse(item['images'])["image_fulltext_caption"];
            if (legend) {
                theTitle = legend;
                if (!theTitle.match(/^\d{2}\/\d{2}/)) {
                    var date = new Date(item.created_date);
                    theTitle = date.toLocaleDateString().replace(/\/?\d{4}\/?/,"") + " " + theTitle;
                }
            }
            return (theTitle);
		},
		getLink: function(item) {
          var link = "/index.php/component/content/article?id="+item.id;
            if (['58','60','61','62'].indexOf(item.catid) != -1) {
              var link = JSON.parse(item['images'])['image_intro_caption'];
            }
			return (link);
		},
		gotoPagina: function(e, pag, func) {
			if (e) e.preventDefault();
			if (pag == this.page || pag == 0 || pag > this.page_total) return false;
			if (pag in this.cache_items) {
				this.items = this.cache_items[pag];
				this.page = pag;
				this.updatePageRange();
				return false;
			}
			this.getList(pag).then(function(data) {
					data = data.map(function(obj, i){obj["index"]=i;return(obj)});
					this.items = data;
					this.cache_items[pag] = data;
					this.page = pag;
					this.updatePageRange();
					if (func) func();
			}.bind(this));
			return false;
		},
		getList: async function(pag) {
			var params = this.params;
			var catIds = encodeURIComponent(JSON.stringify(this.params.catid));
            var url = `index.php?api&command=list&catid=${catIds}&nrows=${params.quantidade}&ordem=${params.ordem}&ordem_direction=${params.ordem_direction}&page=${pag}`;
            if (pag > 1) {
                if (gaUpdate) gaUpdate(url);
 			}
			return (await 
			(await 
			fetch(url)).json());
		}
    }
});
</script>
<?php 
$doc = JFactory::getDocument();
$doc->addScript("https://cdn.jsdelivr.net/npm/vue@2.6.11"); 
?>
