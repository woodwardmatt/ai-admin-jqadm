/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2022
 */


Vue.component('html-editor', {
	template: `<input type="hidden" v-bind:id="id" v-bind:name="name" v-bind:value="value" />`,
	props: ['config', 'editor', 'id', 'name', 'value', 'placeholder', 'readonly', 'tabindex'],

	beforeDestroy: function() {
		if(this.instance) {
			this.instance.destroy();
			this.instance = null;
		}
	},

	data: function() {
		return {
			instance: null,
			content: null
		};
	},

	methods: {
		debounce(func, delay) {
			return function() {
				const context = this;
				const args = arguments;

				clearTimeout(this.timer);
				this.timer = setTimeout(() => func.apply(context, args), delay);
			};
		}
	},

	mounted: function() {
		const config = Object.assign({}, this.config);

		if(this.value) {
			config.initialData = this.value;
		}

		this.editor.create(this.$el, config).then(editor => {
			this.instance = editor;
			editor.isReadOnly = this.readonly;

			const event = this.debounce(ev => {
				this.content = editor.getData();
				if(this.content.match(/<p>/g).length === 1 && this.content.startsWith('<p>') && this.content.endsWith('</p>')) {
					this.content = this.content.replace(/^<p>/, '').replace(/<\/p>$/, '');
				}
				this.$emit('input', this.content, ev, editor);
			}, 300);

			editor.model.document.on('change:data', event);
		} ).catch(err => {
			console.error(err);
		} );
	},

	watch: {
		value(val, oldval) {
			if(val !== oldval && val !== this.content) {
				this.instance.setData(val);
			}
		},

		readonly(val) {
			this.instance.isReadOnly = val;
		}
	}
});
