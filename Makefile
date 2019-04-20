DIST=.dist
PLUGIN_NAME=$(shell basename `pwd`)
VERSION=0.9.2
TARGET=../target

config: clean manifest
	mkdir -p $(DIST)/$(PLUGIN_NAME)
	cp -pr admin.php BUGS CHANGELOG COPYING css imgs include src init.php js language \
	main.inc.php MANIFEST public.php template $(DIST)/$(PLUGIN_NAME)/
	find $(DIST) -name '*~' -exec rm \{\} \;

dist: config
	cd $(DIST); \
	mkdir -p $(TARGET); \
	rm -f $(TARGET)/$(PLUGIN_NAME)-$(VERSION).zip; \
	zip -v -r9 $(TARGET)/$(PLUGIN_NAME)-$(VERSION).zip $(PLUGIN_NAME); \
	cd ..

manifest:
	@find ./ -type f|egrep -v '(*~|.git|.gitignore|.dist|target|.vscode|modele|Makefile|rsync_exclude)'|sed -e 's/\.\///' -e 's/\(.*\)/$(PLUGIN_NAME)\/&/'> ./MANIFEST

clean:
	rm -fr $(DIST)
