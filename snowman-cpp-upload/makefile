#Bernard Ladenthin
#2013-05-01
#bernard.ladenthin@gmail.com
VERSION = 0.1
CC      = /usr/bin/g++
CFLAGS  = --std=c++0x -Wall -Wextra -pedantic-errors -ggdb -DVERSION=\"$(VERSION)\" -D _POSIX_C_SOURCE=200809L
LDFLAGS = -lboost_system -lboost_thread -lboost_program_options -lm -pthread

OBJ = snowman-cpp-upload.o
prefix=/usr

snowman-cpp-upload: $(OBJ)
	$(CC) $(CFLAGS) -o snowman-cpp-upload $(OBJ) $(LDFLAGS)

%.o: %.c
	$(CC) $(CFLAGS) -c $<
run:
	./snowman-cpp-upload
runvalgrind:
	valgrind --tool=memcheck --leak-check=full --show-reachable=yes ./snowman-cpp-upload
rungdb:
	gdb ./snowman-cpp-upload
clean:
	rm *.o
	rm snowman-cpp-upload

install: snowman-cpp-upload
	install -m 0755 snowman-cpp-upload $(prefix)/bin
	install -m 0755 snowman-cpp-upload $(prefix)/local/bin

.PHONY: clean install

